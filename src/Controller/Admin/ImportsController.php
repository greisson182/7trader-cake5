<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Controller\Component\MonthsComponent;
use App\Controller\Component\GlobalComponent;
use Exception;

class ImportsController extends AppController
{

    private function validateAndMapCsvHeaders($headers, $platform = 'profit')
    {
        // Normalizar headers (remover espaços, converter para minúsculas, corrigir encoding)
        $normalizedHeaders = array_map(function ($header) {
            // Tentar corrigir encoding se necessário
            $header = utf8_encode($header);
            return strtolower(trim($header));
        }, $headers);

        // Mapeamento de possíveis nomes de colunas para campos padrão
        $columnPatterns = [
            'asset' => ['ativo'],
            'open_time' => ['abertura'],
            'close_time' => ['fechamento'],
            'trade_duration' => ['tempo operação'],
            'buy_quantity' => ['qtd compra'],
            'sell_quantity' => ['qtd venda'],
            'side' => ['lado'],
            'buy_price' => ['preço compra'],
            'sell_price' => ['preço venda'],
            'market_price' => ['preço de mercado'],
            'mep' => ['mep'],
            'men' => ['men'],
            'buy_agent' => ['ag. compra'],
            'sell_agent' => ['ag. venda'],
            'average_price' => ['médio'],
            'gross_interval_result' => ['res. intervalo bruto'],
            'interval_result_percent' => ['res. intervalo (%)'],
            'operation_number' => ['número operação'],
            'operation_result' => ['res. operação'],
            'operation_result_percent' => ['res. operação (%)'],
            'drawdown' => ['drawdown'],
            'max_gain' => ['ganho max.'],
            'max_loss' => ['perda max.'],
            'tet' => ['tet'],
            'total' => ['total']
        ];

        $columnMapping = [];
        $requiredFields = ['asset', 'open_time', 'operation_result'];
        $foundRequired = [];

        // Mapear cada campo baseado nos padrões
        foreach ($columnPatterns as $field => $patterns) {
            foreach ($patterns as $pattern) {
                $index = array_search($pattern, $normalizedHeaders);
                if ($index !== false) {
                    $columnMapping[$field] = $index;
                    if (in_array($field, $requiredFields)) {
                        $foundRequired[] = $field;
                    }
                    break;
                }
            }
        }

        // Verificar se todos os campos obrigatórios foram encontrados
        $missingFields = array_diff($requiredFields, $foundRequired);
        if (!empty($missingFields)) {
            throw new \Exception('Colunas obrigatórias não encontradas no CSV: ' . implode(', ', $missingFields) .
                '. Verifique se o arquivo possui as colunas: Ativo, Abertura e Res. Operação');
        }

        return $columnMapping;
    }

    public function import_csv()
    {
        $this->request->allowMethod(['post']);

        $this->autoRender = false;

        try {
            // Carregar as tabelas necessárias
            $studiesTable = $this->fetchTable('Studies');
            $operationsTable = $this->fetchTable('Operations');

            if (!$this->request->getData('csv_file')) {
                throw new \Exception('Nenhum arquivo CSV foi enviado.');
            }

            $csvFile = $this->request->getData('csv_file');
            $accountId = $this->request->getData('account_id');
            $platform = $this->request->getData('platform');

            if (!$accountId) {
                throw new \Exception('Tipo de conta é obrigatório.');
            }

            if (!$platform) {
                throw new \Exception('Plataforma é obrigatória.');
            }

            // Verificar se é um arquivo CSV
            if (
                $csvFile->getClientMediaType() !== 'text/csv' &&
                pathinfo($csvFile->getClientFilename(), PATHINFO_EXTENSION) !== 'csv'
            ) {
                throw new \Exception('O arquivo deve ser um CSV válido.');
            }

            // Ler o arquivo CSV
            $csvContent = file_get_contents($csvFile->getStream()->getMetadata('uri'));
            $lines = explode("\n", $csvContent);

            if (empty($lines)) {
                throw new Exception('O arquivo CSV está vazio.');
            }

            // Determinar o student_id
            $user = $this->getCurrentUser();
            if (!$user) {
                throw new Exception('Usuário não autenticado.');
            }

            if ($user->profile_id == 1) { // Admin
                $studentId = $this->request->getData('student_id');
                if (!$studentId) {
                    throw new Exception('Estudante é obrigatório para administradores.');
                }
            } else {
                $studentId = $this->getCurrentStudentId();
                if (!$studentId) {
                    throw new Exception('ID do estudante não encontrado.');
                }
            }

            // Ler o conteúdo do arquivo CSV
            $csvContent = file_get_contents($csvFile->getStream()->getMetadata('uri'));
            $lines = explode("\n", $csvContent);

            $importedCount = 0;
            $errors = [];
            $studiesByDate = []; // Array para agrupar operações por data

            // Processar as linhas do CSV baseado na plataforma
            $headerProcessed = false;
            $csvData = [];
            $columnMapping = []; // Mapeamento das colunas baseado nos títulos

            foreach ($lines as $lineIndex => $line) {

                $line = trim($line);
                if (empty($line)) continue;

                // Processamento específico para plataforma Profit
                if ($platform === 'profit') {
                    // Pular linhas de cabeçalho até encontrar os dados das operações
                    if (!$headerProcessed) {
                        if (strpos($line, 'Ativo') !== false) {
                            // Processar e validar os títulos das colunas
                            $headers = str_getcsv($line, ';');
                            $columnMapping = $this->validateAndMapCsvHeaders($headers, $platform);

                            if (empty($columnMapping)) {
                                throw new Exception('Formato de CSV inválido. Colunas obrigatórias não encontradas.');
                            }

                            $headerProcessed = true;
                            continue; // Pular a linha do cabeçalho
                        }
                        // Capturar informações do cabeçalho
                        if (strpos($line, 'Conta:') !== false) {
                            $csvData['account'] = trim(str_replace('Conta:', '', $line));
                        } elseif (strpos($line, 'Titular:') !== false) {
                            $csvData['holder'] = trim(str_replace('Titular:', '', $line));
                        } elseif (strpos($line, 'Data Inicial:') !== false) {
                            $csvData['date_start'] = trim(str_replace('Data Inicial:', '', $line));
                        } elseif (strpos($line, 'Data Final:') !== false) {
                            $csvData['date_last'] = trim(str_replace('Data Final:', '', $line));
                        }
                        continue;
                    }

                    // Processar linha de dados
                    $data = str_getcsv($line, ';');

                    if (count($data) < 5) {
                        continue; // Pular linhas incompletas
                    }

                    try {
                        // Extrair a data da operação usando o mapeamento de colunas
                        $openTimeIndex = $columnMapping['open_time'] ?? 1;
                        $openTime = !empty($data[$openTimeIndex]) ? $data[$openTimeIndex] : null;
                        if (!$openTime) {
                            $errors[] = "Linha " . ($lineIndex + 1) . ": Data de abertura não encontrada";
                            continue;
                        }

                        // Converter para formato de data
                        $timestamp = strtotime(GlobalComponent::DataHoraDB($openTime));

                        if ($timestamp === false) {
                            $errors[] = "Linha " . ($lineIndex + 1) . ": Data de abertura inválida: $openTime";
                            continue;
                        }

                        $operationDate = date('Y-m-d', $timestamp);

                        // Agrupar operação por data
                        if (!isset($studiesByDate[$operationDate])) {
                            $studiesByDate[$operationDate] = [
                                'operations' => []
                            ];
                        }

                        // Calcular resultado da operação usando o mapeamento
                        $operationResultIndex = $columnMapping['operation_result'] ?? 15;
                        $operationResult = GlobalComponent::MoedaDB($data[$operationResultIndex]) ?? 0;

                        // Determinar mercado usando o mapeamento
                        $assetIndex = $columnMapping['asset'] ?? 0;
                        $assetCompare = GlobalComponent::getTresPrimeirasLetras($data[$assetIndex] ?? '');

                        $marketId = 1;

                        if ($assetCompare == 'WIN') {
                            $marketId = 1;
                        }

                        if ($assetCompare == 'WDO') {
                            $marketId = 2;
                        }

                        // Preparar dados da operação usando o mapeamento de colunas
                        $operationData = [
                            'asset' => $data[$columnMapping['asset'] ?? 0] ?? null,
                            'open_time' => GlobalComponent::DataHoraDB($data[$columnMapping['open_time'] ?? 1]) ?? null,
                            'close_time' => GlobalComponent::DataHoraDB($data[$columnMapping['close_time'] ?? 2]) ?? null,
                            'trade_duration' => GlobalComponent::parseTimeToHMS($data[$columnMapping['trade_duration'] ?? 3] ?? ''),
                            'buy_quantity' => $data[$columnMapping['buy_quantity'] ?? 4] ?? '',
                            'sell_quantity' => $data[$columnMapping['sell_quantity'] ?? 5] ?? '',
                            'side' => $data[$columnMapping['side'] ?? 6] ?? '',
                            'buy_price' => GlobalComponent::MoedaDB($data[$columnMapping['buy_price'] ?? 7] ?? ''),
                            'sell_price' => GlobalComponent::MoedaDB($data[$columnMapping['sell_price'] ?? 8] ?? ''),
                            'market_price' => GlobalComponent::MoedaDB($data[$columnMapping['market_price'] ?? 9] ?? ''),
                            'mep' => isset($columnMapping['mep']) && isset($data[$columnMapping['mep']]) && !empty($data[$columnMapping['mep']]) ? GlobalComponent::MoedaDB($data[$columnMapping['mep']]) : null,
                            'men' => isset($columnMapping['men']) && isset($data[$columnMapping['men']]) && !empty($data[$columnMapping['men']]) ? GlobalComponent::MoedaDB($data[$columnMapping['men']]) : null,
                            'buy_agent' => isset($columnMapping['buy_agent']) && isset($data[$columnMapping['buy_agent']]) ? $data[$columnMapping['buy_agent']] : '',
                            'sell_agent' => isset($columnMapping['sell_agent']) && isset($data[$columnMapping['sell_agent']]) ? $data[$columnMapping['sell_agent']] : '',
                            'average_price' => isset($columnMapping['average']) && isset($data[$columnMapping['average']]) ? mb_convert_encoding($data[$columnMapping['average']], 'UTF-8', 'ISO-8859-1') : '',
                            'gross_interval_result' => isset($columnMapping['gross_interval_result']) && isset($data[$columnMapping['gross_interval_result']]) ? GlobalComponent::MoedaDB($data[$columnMapping['gross_interval_result']]) : null,
                            'interval_result_percent' => isset($columnMapping['interval_result_percent']) && isset($data[$columnMapping['interval_result_percent']]) ? GlobalComponent::MoedaDB($data[$columnMapping['interval_result_percent']]) : null,
                            'operation_number' => isset($columnMapping['operation_number']) && isset($data[$columnMapping['operation_number']]) ? $data[$columnMapping['operation_number']] : '',
                            'operation_result' => GlobalComponent::MoedaDB($data[$columnMapping['operation_result'] ?? 18] ?? 0),
                            'operation_result_percent' => isset($columnMapping['operation_result_percent']) && isset($data[$columnMapping['operation_result_percent']]) ? GlobalComponent::MoedaDB($data[$columnMapping['operation_result_percent']]) : null,
                            'drawdown' => isset($columnMapping['drawdown']) && isset($data[$columnMapping['drawdown']]) ? GlobalComponent::MoedaDB($data[$columnMapping['drawdown']]) : null,
                            'max_gain' => isset($columnMapping['max_gain']) && isset($data[$columnMapping['max_gain']]) ? GlobalComponent::MoedaDB($data[$columnMapping['max_gain']]) : null,
                            'max_loss' => isset($columnMapping['max_loss']) && isset($data[$columnMapping['max_loss']]) ? GlobalComponent::MoedaDB($data[$columnMapping['max_loss']]) : null,
                            'tet' => isset($columnMapping['tet']) && isset($data[$columnMapping['tet']]) ? GlobalComponent::parseTimeToHMS($data[$columnMapping['tet']]) : null,
                            'total' => isset($columnMapping['total']) && isset($data[$columnMapping['total']]) ? GlobalComponent::MoedaDB($data[$columnMapping['total']]) : null,
                            'account' => $csvData['account'] ?? '',
                            'holder' => $csvData['holder'] ?? '',
                            'date_start' => GlobalComponent::DataDB($csvData['date_start'] ?? null),
                            'date_last' => GlobalComponent::DataDB($csvData['date_last'] ?? null),
                            'market_id' => $marketId,
                        ];

                        $studiesByDate[$operationDate]['operations'][] = $operationData;
                    } catch (\Exception $e) {
                        $errors[] = "Linha " . ($lineIndex + 1) . ": " . $e->getMessage();
                    }
                }
            }

            //pr($studiesByDate);die;

            // Processar cada data agrupada
            foreach ($studiesByDate as $date => $dateData) {
                try {
                    // Agrupar operações por mercado para esta data
                    $operationsByMarket = [];
                    foreach ($dateData['operations'] as $operationData) {
                        $marketId = $operationData['market_id'];
                        if (!isset($operationsByMarket[$marketId])) {
                            $operationsByMarket[$marketId] = [
                                'operations' => [],
                                'wins' => 0,
                                'losses' => 0,
                                'profit_loss' => 0.00
                            ];
                        }
                        
                        // Contar cada operação individual como win ou loss
                        $operationResult = $operationData['operation_result'];
                        if ($operationResult > 0) {
                            $operationsByMarket[$marketId]['wins']++;
                        } elseif ($operationResult < 0) {
                            $operationsByMarket[$marketId]['losses']++;
                        } else {
                            // Se for zero, considerar como win conforme solicitado
                            $operationsByMarket[$marketId]['wins']++;
                        }
                        
                        $operationsByMarket[$marketId]['profit_loss'] += $operationResult;
                        $operationsByMarket[$marketId]['operations'][] = $operationData;
                    }

                    // Criar ou atualizar um study para cada mercado nesta data
                    foreach ($operationsByMarket as $marketId => $marketData) {
                        // Verificar se já existe um estudo para esta data e mercado
                        $existingStudy = $studiesTable->find()
                            ->where([
                                'student_id' => $studentId,
                                'market_id' => $marketId,
                                'account_id' => $accountId,
                                'study_date' => $date
                            ])
                            ->first();

                        if ($existingStudy) {
                            // Usar estudo existente e somar aos valores existentes
                            $studyId = $existingStudy->id;

                            // Atualizar contadores do estudo existente (somando aos valores existentes)
                            $existingStudy = $studiesTable->patchEntity($existingStudy, [
                                'wins' => $existingStudy->wins + $marketData['wins'],
                                'losses' => $existingStudy->losses + $marketData['losses'],
                                'profit_loss' => $existingStudy->profit_loss + $marketData['profit_loss'],
                            ]);
                            $studiesTable->save($existingStudy);
                        } else {
                            // Criar novo estudo para esta data e mercado
                            $studyData = [
                                'student_id' => $studentId,
                                'market_id' => $marketId,
                                'account_id' => $accountId,
                                'study_date' => $date,
                                'wins' => $marketData['wins'],
                                'losses' => $marketData['losses'],
                                'profit_loss' => $marketData['profit_loss'],
                            ];

                            $study = $studiesTable->newEmptyEntity();
                            $study = $studiesTable->patchEntity($study, $studyData);

                            if (!$studiesTable->save($study)) {
                                throw new \Exception("Erro ao criar estudo para a data $date.");
                            }

                            $studyId = $study->id;
                        }

                        // Salvar todas as operações deste mercado/data
                        foreach ($marketData['operations'] as $operationData) {
                            $operationData['study_id'] = $studyId;

                            // Debug: verificar se o study_id é válido
                            if (empty($studyId)) {
                                $errors[] = "Study ID vazio para a data $date";
                                continue;
                            }

                            $operation = $operationsTable->newEmptyEntity();
                            $operation = $operationsTable->patchEntity($operation, $operationData);

                            if ($operationsTable->save($operation)) {
                                $importedCount++;
                            } else {
                                // Debug: capturar erros de validação
                                $validationErrors = $operation->getErrors();

                                $errorMsg = "Erro ao salvar operação da data $date";
                                if (!empty($validationErrors)) {
                                    $errorMsg .= ": " . json_encode($validationErrors);
                                }
                                $errors[] = $errorMsg;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Erro ao processar data $date: " . $e->getMessage();
                }
            }

            $studiesCount = count($studiesByDate);
            $message = "$importedCount operações importadas com sucesso em $studiesCount estudo(s) para a plataforma $platform.";
            if (!empty($errors)) {
                $message .= " Erros: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " e mais " . (count($errors) - 3) . " erros.";
                }
            }

            $response = [
                'success' => true,
                'message' => $message,
                'imported_count' => $importedCount,
                'studies_count' => $studiesCount,
                'errors_count' => count($errors)
            ];

            $this->response = $this->response->withType('application/json');
            echo json_encode($response);
            return;
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            $this->response = $this->response->withType('application/json');
            echo json_encode($response);
            return;
        }
    }
}
