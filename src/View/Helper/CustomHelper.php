<?php

namespace App\View\Helper;

use Cake\View\Helper;

class CustomHelper extends Helper
{

	public function Words($String, $Limite, $Pointer = null)
	{
		$Data = strip_tags(trim($String));
		$Format = (int) $Limite;

		$ArrWords = explode(' ', $Data);
		$NumWords = count($ArrWords);
		$NewWords = implode(' ', array_slice($ArrWords, 0, $Format));

		$Pointer = (empty($Pointer) ? '...' : ' ' . $Pointer);
		$Result = ($Format < $NumWords ? $NewWords . $Pointer : $Data);
		return $Result;
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

	public function Image($image_url = '')
	{

		$external_link = $image_url;
		if (@getimagesize($external_link)) {
			$image = true;
		} else {
			$image = false;
		}

		return $image;
	}

	public function MoedaView($valor)
	{

		$valorTratado = "R$ " .  number_format($valor, 2, ',', '.');

		return $valorTratado;
	}

	public function taxa($valor, $size = 2)
	{

		$valorTratado = number_format($valor, $size, ',', '.');

		return $valorTratado;
	}

	public function taxaView($valor, $decimal = 5)
	{

		$valorTratado = number_format($valor, $decimal, ',', '.');

		return $valorTratado;
	}

	public function taxaDB($valor)
	{

		if ($valor) {

			$valorTratado = trim(str_replace(',', '.', $valor));
		} else {
			$valorTratado = 0;
		}

		return $valorTratado;
	}

	public function DataHoraView($data, $segundos = false)
	{

		if ($data) {

			if ($segundos) {
				$valorTratado = date("d/m/Y H:i:s", strtotime($data));
			} else {
				$valorTratado = date("d/m/Y H:i", strtotime($data));
			}
		} else {
			$valorTratado = "";
		}

		return $valorTratado;
	}

	public function DataHoraDB($data)
	{

		$DataHora = explode(" ", $data);

		$Hora = explode(":", $DataHora[1]);

		if (sizeof($Hora) == 2)
			$DataHora[1] . ":00";

		$NewData = implode("-", array_reverse((explode("/", $DataHora[0]))));

		$valorTratado = $NewData . " " . $DataHora[1];

		return $valorTratado;
	}

	public function DataView($data)
	{

		if ($data) {

			$valorTratado = date("d/m/Y", strtotime($data));
		} else {
			$valorTratado = "";
		}

		return $valorTratado;
	}

	public function DataDB($data)
	{

		$valorTratado = implode("-", array_reverse((explode("/", $data))));

		return $valorTratado;
	}

	public function slug($str)
	{
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		$str = str_ireplace($a, $b, $str);

		$str = strtolower(trim($str));
		$str = preg_replace('/[^a-z0-9-]/', '-', $str);
		$str = preg_replace('/-+/', "-", $str);

		return trim($str, "-");
	}

	public function formatarTempo($tempo)
	{
		// Separa as partes por ":"
		list($horas, $minutos, $segundos) = explode(":", $tempo);

		// Remove zeros à esquerda
		$horas = ltrim($horas, "0");
		$minutos = ltrim($minutos, "0");
		$segundos = ltrim($segundos, "0");

		// Monta a string final
		$resultado = "";
		if ($horas !== "") $resultado .= $horas . "h";
		if ($minutos !== "") $resultado .= $minutos . "m";
		if ($segundos !== "") $resultado .= $segundos . "s";

		return $resultado;
	}
}
