<?php

namespace helena\classes;

use helena\services\common\BaseService;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use minga\framework\CreativeCommons;
use minga\framework\Context;
use minga\framework\AttributeEntity;
use minga\framework\Str;
use helena\db\frontend\MetadataModel;
use helena\db\frontend\FileModel;
use helena\classes\App;
use minga\framework\IO;

// Documentation: https://mpdf.github.io/
class PdfFile
{
	private $mpdf;

	public function __construct()
	{
	$this->mpdf = $this->createPdfInstance();
		$this->mpdf->SetHTMLFooter('<div class="footer">
						<table width=100% cellspacing=0 cellpadding=0><tr><td class="footerPageNumber" align="center" style="width: 70px; padding: 5px 12px 5px 12px; background-color: #e0e0e0;">
<div class="footerPageNumber">
							{PAGENO}
						</div></td>
<td style="padding: 7px 12px 7px 12px; background-color: #efefef;"><div class="footerRow1">
							Poblaciones
						</div><div class="footerRow2">
							Plataforma abierta de datos espaciales de la Argentina
						</div></td></tr>
						</table></div>');
	}
	public function WriteMainTitle($title)
	{
		// Pone el encabezado para todas las páginas
		$this->mpdf->SetHTMLHeader("<div class='header'>" . $this->HtmlEncode($title, true) . "</div>");
		// Pisa con el título de la primera
		$titleHtml = "<h1>" . $this->HtmlEncode($title, true) . "</h1>";
		$this->mpdf->WriteHTML("<div class='mainTitle'>" . $titleHtml . "</div>");
		// avanza el contenido de la primera en función del tamaño del título
		$height = $this->measureHeight($titleHtml, false);
		$this->mpdf->y += $height - 65; 
	}

	private function measureHeight($html, $escape)
	{
		$temp =  $this->createPdfInstance();
		$a = $temp->y;
		$temp->WriteHTML($this->HtmlEncode($html, $escape));
		$b = $temp->y;
		return $b - $a;
	}
	
	private function createPdfInstance()
	{
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4',
																'margin_left' => 30,
																'margin_right' => 30,
																'margin_top' => 30,
																'margin_bottom' => 25.4,
																'margin_header' => 9,
																'margin_footer' => 14,
																'shrink_tables_to_fit' => false]);
		$this->writeStyles($mpdf);
		return $mpdf;
	}

	public function Save($filename = "")
	{
		if ($filename == "")
			$filename = IO::GetTempFilename();
		$this->mpdf->Output($filename);
		return $filename;
	}

	public function AddImage($tag, $content)
	{
		$this->mpdf->imageVars[$tag] = $content;
	}
	public function WriteHeading1($text, $escape = true)
	{
		$this->mpdf->WriteHTML("<h1>" . $this->HtmlEncode($text, $escape) . "</h1>");
	}
	public function WriteHeading4($text, $escape = true)
	{
		$this->mpdf->WriteHTML("<h4>" . $text . "</h4>");
	}
	public function WriteIndentedText($text, $escape = true)
	{
		$this->mpdf->WriteHTML("<div class='indentedText'>" . $this->HtmlEncode($text, $escape) . "</div>");
	}
	public function WriteDoubleIndentedText($text, $escape = true)
	{
		$this->mpdf->WriteHTML("<div class='doubleIndentedText'>" . $this->HtmlEncode($text, $escape) . "</div>");
	}
	public function WriteIndentedMail($mail)
	{
		if ($mail != '')
				$this->WriteIndentedPair('Correo electrónico', $this->FormatEmail($mail), false);
	}
	public function WriteDoubleIndentedMail($mail)
	{
		if ($mail != '')
				$this->WriteDoubleIndentedPair('Correo electrónico', $this->FormatEmail($mail), false);
	}
	public function WriteIndentedPair($label, $text, $escape = true, $addDots = true)
	{
		$text = trim($text);
		if ($text == "")
			return;
		if (Str::EndsWith($text, ".") == false && $addDots) $text .= ".";
		if ($addDots) $label .= ":";

		$this->WriteIndenterTable($label, $text, "1", $escape);
	}
	public function WriteIndentedPairTitle($label, $text, $escape = true)
	{
		$text = trim($text);
		if ($text == "")
			return;
		$this->WriteIndenterTable($label, $text, "1", $escape, 'subTableHeader');
	}

	private function WriteIndenterTable($text1, $text2, $level, $escape, $extraStyle = '')
	{
		$html = "<table cellpadding=0 cellspacing=0 style='overflow: wrap'><tr><td valign='top' class='fixedIndentedTextBlock" . $level . " " . $extraStyle . "'>" . $this->HtmlEncode($text1, $escape)
								. "</td><td valign='top' class='fixedIndentedTextBlockEnd " . $extraStyle . "'>" . $this->HtmlEncode($text2, $escape) . "</td></tr></table>";
		$this->mpdf->WriteHTML($html);
	}

	public function WriteDoubleIndentedPair($label, $text, $escape = true, $addDots = true)
	{
		$text = trim($text);
		if ($text == "")
			return;
		if ($addDots && Str::EndsWith($text, ".") == false) $text .= ".";
		$this->WriteIndenterTable($label . ":", $text, "2", $escape);
	}
	
	public function WriteExtraIndentedPair($label, $text, $escape = true, $addDots = true)
	{
		$text = trim($text);
		if ($text == "")
			return;
		if ($addDots && Str::EndsWith($text, ".") == false) $text .= ".";
		$this->WriteIndenterTable($label . ":", $text, "15", $escape);
	}
	
	public function WriteIndentedSeparator()
	{
		$this->WriteIndentedText("<p style='font-size: 5pt'>________________________________________________________________________________________________________________________________________________________________________</p>", false);
	}

	public function WriteExtraIndentedSpace()
	{
		$this->WriteIndentedText("<div style='height: 6px'></div>", false);
	}

	public function WriteExtraIndentedSeparator()
	{
		$this->WriteIndentedText("<p style='font-size: 5pt; margin-left: 60px'>_________________________________________________________________________________________________________________________________________________</p>", false);
	}

	public function WriteIndentedSpace()
	{
		$this->WriteIndentedText("<div style='height: 10pt'></div>", false);
	}
	public function WriteIndentedPairLink($label, $url, $escape = true)
	{
		$label = trim($label);
		$link = $this->FormatLink($url, $url);
		$this->WriteIndentedPair($label, $link, false, false);
	}
	public function WritePair($label, $text, $escape = true)
	{
		$this->WriteHeading4($label);
		$text = trim($text);
		if ($text == "")
			return;
		$text = $this->HtmlEncode($text, $escape);
		$text = Str::Replace($text, "\n", "<br>");

		if (Str::StartsWith($text, 'http'))
			$text = "<a class='link' href='" . $text . "'>" . $text . '</a>';
		if (Str::EndsWith($text, ".") == false) $text .= ".";
		$this->WriteIndentedText($text, false);
	}

	function HtmlEncode($text, $escape = true)
	{
		if ($escape == false) return $text;
		$text = Str::Replace($text, "&", "&amp;");
		$text = Str::Replace($text, "<", "&lt;");
		$text = Str::Replace($text, ">", "&gt;");
		// rescata las itálicas de citado
		$text = Str::Replace($text, "&lt;i&gt;", "<i>");
		$text = Str::Replace($text, "&lt;/i&gt;", "</i>");
		return $text;
	}

	public function FormatEmail($email)
	{
		return $this->FormatLink('mailto:' . $email, $email);
	}
	public function FormatLink($url, $text)
	{
		return "<a href='" . $this->HtmlEncode($url). "'>" . $this->HtmlEncode($text) . "</a>";
	}

	private function writeStyles($mpdf)
	{
		// Como en http://www.vesna.ru/php5/examples/example02_CSS_styles.php

		$fieldWidth = 90;
		$firstPadding = 45;
		$bottomMargin = 5;
		$styles = "body { font-family: DejaVuSansCondensed, sans-serif; font-size: 10pt;  }
								p { 	text-align: justify; margin-bottom: 4pt;  margin-top:0pt; }

								hr {	width: 70%; height: 1px;
									text-align: center; color: #999999;
									margin-top: 8pt; margin-bottom: 8pt; }

								a {	color: #000066; font-style: normal; text-decoration: none;
									font-weight: normal; }

								ul {	text-indent: 5mm; margin-bottom: 9pt; }
								ol {	text-indent: 5mm; margin-bottom: 9pt; }

								.link {	color: #000066; font-style: normal; text-decoration: none;
									font-weight: normal; }


								.indentedText { padding-bottom: " . $bottomMargin . "pt; line-height: 1.6em;  text-align: justify; margin-left: " . $firstPadding. "pt;  }
								.doubleIndentedText { padding-bottom: " . $bottomMargin . "pt; line-height: 1.6em;  text-align: justify; margin-left: " . ($firstPadding + $fieldWidth) . "pt;  }
								.fixedIndentedTextBlock1 { padding-bottom: " . $bottomMargin . "pt; line-height: 1.6em;  text-align: left; padding-left: " . $firstPadding . "pt; width: " . ($fieldWidth + $firstPadding) . "pt;  }
								.fixedIndentedTextBlock15 { padding-bottom: " . $bottomMargin . "pt; line-height: 1.6em;  text-align: left; padding-left: " . ($firstPadding + $fieldWidth / 2) . "pt; width: " . ($firstPadding + $fieldWidth * 1.5) . "pt;  }
								.fixedIndentedTextBlock2 { padding-bottom: " . $bottomMargin . "pt; line-height: 1.6em;  text-align: left; padding-left: " . ($firstPadding + $fieldWidth) . "pt; width: " . ($firstPadding + $fieldWidth * 2) . "pt;  }
								.fixedIndentedTextBlockEnd { padding-bottom: " . $bottomMargin . "pt; line-height: 1.6em; width: 100%;  text-align: justify;  }

								.subTableHeader { font-weight: bold };

								pre { font-family: DejaVuSansMono, monospaced; font-size: 9pt; margin-top: 5pt; margin-bottom: 5pt; }
	
			.mainTitle {
								width: 100%; position: absolute; 
								top: -2px; background-color: white;
								}		
	.header {	
									margin-left: -200px; margin-right: -200px; background-color: #979797;
									padding: 5px 200px 5px 200px;
									font-weight: normal; font-size: 12pt; color: #FFF;
									font-family: DejaVuSansCondensed, sans-serif; margin-top: 18pt; margin-bottom: 16pt;
									text-align: right;  page-break-after:avoid; }
						
	h1 {	
									margin-left: -200px; background-color: #979797;
									padding: 50px 200px 10px 200px;
									font-weight: normal; font-size: 18pt; color: #FFF;
									font-family: DejaVuSansCondensed, sans-serif; margin-top: 0pt; margin-bottom: 16pt;
									text-align: left;  page-break-after:avoid; }

.footer {	
									position: absolute; left: 0px; color: #666;
									padding: 0px; width: 100% }
.footerRow1 {	
									margin-left: 40px; margin-top: 0px; font-weight: normal; font-size: 20pt; 
									font-family: DejaVuSansCondensed, sans-serif; text-transform: uppercase;
									text-align: left;  page-break-after:avoid; color: #888; }
.footerRow2 {	
									margin-left: 40px; font-weight: normal; font-size: 7pt; 
									font-family: DejaVuSansCondensed, sans-serif;
									text-align: left;  page-break-after:avoid; color: #888; }
.footerPageNumber {	
									margin-left: 0px; font-weight: normal; font-size: 27pt; 
									font-family:\'DejaVu Sans Condensed\', DejaVuSansCondensed, sans-serif;
									text-align: left;  page-break-after:avoid; color: #666; }

								h2 {	font-weight: bold; font-size: 12pt; color: #000066;
									font-family: DejaVuSansCondensed, sans-serif; margin-top: 6pt; margin-bottom: 6pt;
									border-top: 0.07cm solid #000000; border-bottom: 0.07cm solid #000000;
									text-align: ;  text-transform: uppercase; page-break-after:avoid; }
								h3 {	font-weight: normal; font-size: 26pt; color: #000000;
									font-family: DejaVuSansCondensed, sans-serif; margin-top: 0pt; margin-bottom: 6pt;
									border-top: 0; border-bottom: 0;
									text-align: ; page-break-after:avoid; }
								h4 {	font-weight: normal; font-size: 11pt; color: #444;
									font-family: DejaVuSansCondensed, sans-serif; margin-top: 2pt; margin-bottom: 2pt;
									text-align: ;  margin-collapse:collapse; page-break-after:avoid; }
								h5 {	font-weight: bold; font-style:italic; ; font-size: 11pt; color: #000044;
									font-family: DejaVuSansCondensed, sans-serif; margin-top: 8pt; margin-bottom: 4pt;
									text-align: ;  page-break-after:avoid; }
								h6 {	font-weight: bold; font-size: 9.5pt; color: #333333;
									font-family: DejaVuSansCondensed, sans-serif; margin-top: 6pt; margin-bottom: ;
									text-align: ;  page-break-after:avoid; }


								.breadcrumb {
									text-align: right; font-size: 8pt; font-family: DejaVuSerifCondensed, serif; color: #666666;
									font-weight: bold; font-style: normal; margin-bottom: 6pt; }

								.infobox { margin-top:10pt; background-color:#DDDDBB; text-align:center; border:1px solid #880000; }

								.big { font-size: 1.6em; }
								.red { color: #880000; }
								.slanted { font-style: italic; }";
							// border-top: 0.075cm solid #000000; border-bottom: 0.075cm solid #000000;

		$mpdf->WriteHTML($styles, 1);
	}
}
