<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use stdClass;

class GlobalComponent extends Component
{
	public function formatCnpjCpf($value)
	{
		$CPF_LENGTH = 11;
		$cnpj_cpf = preg_replace("/\D/", '', $value);

		if (strlen($cnpj_cpf) === $CPF_LENGTH) {
			return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
		}

		return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
	}

	public function removeCaracteres($valor)
	{

		if ($valor) {

			$valorTratado = trim(str_replace('(', '', str_replace(')', '', str_replace('/', '', (str_replace('.', '', str_replace('-', '', str_replace('_', '', str_replace('', '', $valor)))))))));
		} else {
			$valorTratado = '';
		}
		return $valorTratado;
	}

	public function trataTelefone($valor)
	{
		if ($valor) {

			$valorTratado = trim(str_replace('(', '', str_replace(')', '', str_replace('/', '', (str_replace('.', '', str_replace('-', '', $valor)))))));
		} else {
			$valorTratado = '';
		}
		return explode(' ', $valorTratado);
	}

	public function MoedaDB($valor)
	{

		if ($valor) {

			$valorTratado = trim(str_replace(',', '.', (str_replace('.', '', str_replace('R$ ', '', $valor)))));
		} else {
			$valorTratado = 0;
		}
		return $valorTratado;
	}

	public function MoedaDBDolar($valor)
	{

		if ($valor) {

			$valorTratado = trim(str_replace(',', '', str_replace('$', '', str_replace('R$', '', str_replace('-', '', $valor)))));
		} else {
			$valorTratado = 0;
		}
		return $valorTratado;
	}

	public function MoedaDB2($valor)
	{
		if ($valor) {
			// Remove "R$ " e "$" do valor
			$valorLimpo = str_replace(['R$ ', 'R$', '$'], '', $valor);

			// Se existir vírgula, trata como formato brasileiro
			if (strpos($valorLimpo, ',') !== false) {
				// Remove pontos de milhar e troca vírgula por ponto decimal
				$valorTratado = str_replace(',', '.', str_replace('.', '', $valorLimpo));
			} else {
				// Apenas troca vírgula por ponto, se houver
				$valorTratado = str_replace(',', '.', $valorLimpo);
			}

			$valorTratado = trim($valorTratado);
		} else {
			$valorTratado = 0;
		}

		return $valorTratado;
	}

	public function MoedaView($valor, $decimal = 2)
	{

		if ($valor) {

			$valorTratado = "R$ " .  number_format($valor, $decimal, ',', '.');
		} else {
			$valorTratado = "R$ 0,00";
		}

		return $valorTratado;
	}

	public function taxaView($valor, $decimal = 2)
	{

		$valor = (float)$valor;

		$valorTratado = number_format($valor, $decimal, ',', '.');

		return $valorTratado;
	}

	public function taxaDB($valor, $round = 2)
	{

		if ($valor) {

			$valorTratado = round(trim(str_replace(',', '.', str_replace('.', '', $valor))), $round);
		} else {
			$valorTratado = 0;
		}

		return $valorTratado;
	}

	public function DataHoraView($data, $segundos = false)
	{

		if ($segundos) {
			$valorTratado = date("d/m/Y H:i:s", strtotime($data));
		} else {
			$valorTratado = date("d/m/Y H:i", strtotime($data));
		}

		return $valorTratado;
	}

	public function DataHoraDB($data)
	{
		$DataHora = explode(" ", $data);

		$Hora = explode(":", $DataHora[1]);

		if (sizeof($Hora) == 2)
			$DataHora[1] .= ":00";

		$NewData = implode("-", array_reverse((explode("/", $DataHora[0]))));

		$valorTratado = $NewData . " " . $DataHora[1];

		return $valorTratado;
	}

	public static function DataView($data)
	{
		$valorTratado = date("d/m/Y", strtotime($data));

		return $valorTratado;
	}

	public function DataDB($data)
	{
		if ($data) {
			$valorTratado = implode("-", array_reverse(explode("/", $data)));
		} else {
			$valorTratado = null;
		}

		return $valorTratado;
	}

	public function DataDBXls($data)
	{
		if ($data) {
			$valor = explode("/", $data);

			if (strlen($valor[0]) == 1) {
				$valor[0] = '0' . $valor[0];
			}

			$valorTratado = $valor[2] . '-' . $valor[0] . '-' . $valor[1];
		} else {
			$valorTratado = null;
		}

		return $valorTratado;
	}

	public function validar_cpf($cpf)
	{
		$cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
		// Valida tamanho
		if (strlen($cpf) != 11)
			return false;
		// Calcula e confere primeiro dígito verificador
		for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--)
			$soma += $cpf[$i] * $j;
		$resto = $soma % 11;
		if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto))
			return false;
		// Calcula e confere segundo dígito verificador
		for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--)
			$soma += $cpf[$i] * $j;
		$resto = $soma % 11;
		return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto);
	}

	public function validar_cnpj($cnpj)
	{

		$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
		// Valida tamanho
		if (strlen($cnpj) != 14)
			return false;
		// Valida primeiro dígito verificador
		for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
			return false;
		// Valida segundo dígito verificador
		for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
	}

	public function slug($str)
	{
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		$str = str_ireplace($a, $b, $str);

		$str = strtolower(trim($str));
		$str = preg_replace('/[^a-z0-9-]/', '.', $str);
		$str = preg_replace('/-+/', ".", $str);

		return trim($str, "-");
	}

	public function month($num, $curto = false)
	{
		$int = intval($num);

		if ($curto) {
			$mes[1] = "Jan";
			$mes[2] = "Fev";
			$mes[3] = "Mar";
			$mes[4] = "Abr";
			$mes[5] = "Mai";
			$mes[6] = "Jun";
			$mes[7] = "Jul";
			$mes[8] = "Ago";
			$mes[9] = "Set";
			$mes[10] = "Out";
			$mes[11] = "Nov";
			$mes[12] = "Dez";
		} else {

			$mes[1] = "Janeiro";
			$mes[2] = "Fevereiro";
			$mes[3] = "Março";
			$mes[4] = "Abril";
			$mes[5] = "Maio";
			$mes[6] = "Junho";
			$mes[7] = "Julho";
			$mes[8] = "Agosto";
			$mes[9] = "Setembro";
			$mes[10] = "Outubro";
			$mes[11] = "Novembro";
			$mes[12] = "Dezembro";
		}

		if (isset($mes[$int]))
			return $mes[$int];
		else
			return "";
	}

	public function week($num, $curto = false)
	{
		$int = intval($num);

		$sem[0] = array("Domingo", "DOM");
		$sem[1] = array("Segunda-Feira", "SEG");
		$sem[2] = array("Terça-Feira", "TER");
		$sem[3] = array("Quarta-Feira", "QUA");
		$sem[4] = array("Quinta-Feira", "QUI");
		$sem[5] = array("Sexta-Feira", "SEX");
		$sem[6] = array("Sábado", "SAB");

		if (isset($sem[$int]))
			if ($curto)
				return $sem[$int][1];
			else
				return $sem[$int][0];
		else
			return "";
	}

	public function formatarDocumento($documento)
	{
		// Remove qualquer caractere que não seja número
		$documento = preg_replace('/[^0-9]/', '', $documento);

		// Verifica o número de dígitos e aplica a formatação apropriada
		if (strlen($documento) == 11) {
			// Formatar CPF (000.000.000-00)
			return substr($documento, 0, 3) . '.' . substr($documento, 3, 3) . '.' . substr($documento, 6, 3) . '-' . substr($documento, 9, 2);
		} elseif (strlen($documento) == 14) {
			// Formatar CNPJ (00.000.000/0000-00)
			return substr($documento, 0, 2) . '.' . substr($documento, 2, 3) . '.' . substr($documento, 5, 3) . '/' . substr($documento, 8, 4) . '-' . substr($documento, 12, 2);
		}

		return false; // Documento inválido
	}

	public function substituirImagem($string)
	{
		// Expressão regular para capturar {img:URL}
		$pattern = '/\{img:(https?:\/\/[^\}]+)\}/';

		// Função de substituição
		$string = preg_replace_callback($pattern, function ($matches) {
			// O $matches[1] contém a URL capturada
			$urlImagem = $matches[1];

			// Retornando o código HTML da imagem com a URL capturada
			return "<br /><a href='" . $urlImagem . "' target='_blank'> <img src='" . $urlImagem . "' alt='Imagem' class='img-responsive img-history'></a>";
		}, $string);

		return $string;
	}

	public function downloadFile($url, $savePath)
	{
		// Abre o arquivo remoto
		$fileContents = file_get_contents($url);

		$response = true;

		// Verifica se o conteúdo foi obtido
		if ($fileContents === false) {
			$response = false;
		}

		// Salva o conteúdo no caminho especificado
		$result = file_put_contents($savePath, $fileContents);

		// Verifica se o arquivo foi salvo com sucesso
		if ($result === false) {
			$response = false;
		}

		return $response;
	}

	public function convertUrlToBase64($url)
	{

		$success = new stdClass;
		$success->status = true;

		// Obtém o conteúdo do arquivo da URL
		$fileContents = file_get_contents($url);

		// Verifica se o arquivo foi acessado com sucesso
		if ($fileContents === false) {
			$success = false;
		}

		// Converte o conteúdo do arquivo para Base64
		$success->file64 = base64_encode($fileContents);

		return $success;
	}

	public function trataImagensBase64($html)
	{
		$dom = new \DOMDocument();
		libxml_use_internal_errors(true); // Evita warnings de HTML mal formatado
		$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

		$imgs = $dom->getElementsByTagName('img');

		foreach ($imgs as $img) {
			$src = $img->getAttribute('src');

			if (strpos($src, 'data:image/') === 0) {
				// Detecta extensão
				preg_match('/^data:image\/(.*?);base64,/', $src, $match);
				$extensao = isset($match[1]) ? strtolower($match[1]) : 'png';

				// Remove o prefixo data:
				$base64 = substr($src, strpos($src, ',') + 1);
				$base64 = str_replace(' ', '+', $base64); // Corrige espaços

				$dados = base64_decode($base64);

				// Gera nome único
				$filename = uniqid('img_', true) . '.' . $extensao;

				$uploadFolder  = 'upload';
				$uploadPath  = WWW_ROOT . $uploadFolder;

				if (!is_dir($uploadPath))
					mkdir($uploadPath, 0777);
				if (!is_dir("$uploadPath/" . date("Y")))
					mkdir("$uploadPath/" . date("Y"), 0777);
				if (!is_dir("$uploadPath/" . date("Y") . "/" . date("m")))
					mkdir("$uploadPath/" . date("Y") . "/" . date("m"), 0777);
				if (!is_dir("$uploadPath/" . date("Y") . "/" . date("m") . "/" . date("d")))
					mkdir("$uploadPath/" . date("Y") . "/" . date("m") . "/" . date("d"), 0777);

				$filePath =  $uploadFolder . "/" . date("Y") . "/" . date("m") . "/" . date("d") . "/" . $filename;

				file_put_contents($filePath, $dados);

				$urlImagem = HOME . $filePath;

				// Atualiza src no DOM
				$img->setAttribute('src', $urlImagem);
			}
		}

		// Retorna o HTML com as imagens trocadas
		return $dom->saveHTML($dom->getElementsByTagName('body')->item(0));
	}

	function tratarCelular(string $celular): ?int
	{
		// Remove qualquer caractere que não seja número
		$limpo = preg_replace('/\D/', '', $celular);

		// Verifica se tem tamanho válido (10 ou 11 dígitos)
		$tamanho = strlen($limpo);
		if ($tamanho < 10 || $tamanho > 11) {
			return null; // Número inválido
		}

		return (int)$limpo;
	}

	public function tipoDocumento($valor)
	{
		$valor = preg_replace('/\D/', '', $valor);

		if (strlen($valor) === 11) {
			return 'CPF';
		} elseif (strlen($valor) === 14) {
			return 'CNPJ';
		} else {
			return null;
		}
	}
}
