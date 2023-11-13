<?php
namespace OCA\FilesWm\Controller;

use Exception;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\File;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use Throwable;

class FilesWmController extends OCSController {

    public function __construct(
        string             $appName,
        IRequest           $request,
        private ?string    $userId
    ) {
        parent::__construct($appName, $request);
    }

	/**
     * @NoAdminRequired
     * @param int $iddoc
     * @return DataResponse
     */
	public static function newDownloadWithWaterMark(int $id): DataResponse {
		// initiate FPDI
		$pdf = new Fpdi();
		$a = \OC::$server->getRootFolder()->getById($id);
		$file = $a[0];
	
		$source = $file->fopen('rb');
		if($source) {
			$line_first = fgets($source);
		}
		else{
			echo "error opening the file.";
		}
		// extract number such as 1.4,1.5 from first read line of pdf file
		preg_match_all('!\d+!', $line_first, $matches);
							
		// save that number in a variable
		$pdfversion = implode('.', $matches[0]);
		$file_fullname = $file->getName();
		// var_dump($pdfversion);
		if($pdfversion > "1.4"){
			fclose($source);
			$fileInput = $file->stat()['full_path'];
			$tmp = explode('.',$file_fullname);
			$n = count($tmp);
			$file_name = $tmp[0];
			$file_extension = $tmp[$n-1];
			$file_new_fullname = $file_name . '_tmp.' . $file_extension; 
			$fileOutput = str_replace($file_fullname,$file_new_fullname,$fileInput);
			shell_exec('gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile="'.$fileOutput.'" "'.$fileInput.'"'); 
			$pagecount = $pdf->setSourceFile($fileOutput);
			self::loopPages($pdf,$pagecount);
			$pdf->Output('D',$file_fullname);
			$new_file = fopen($fileOutput,'w');
			fclose($new_file);
			unlink($fileOutput);
		} else {
			$pagecount = $pdf->setSourceFile($source);
			self::loopPages($pdf,$pagecount);
			$pdf->Output('D',$file_fullname);
			fclose($source);
		}
	}

	static function loopPages($pdf,$pagecount){
		for ($i = 1; $i <= $pagecount; $i ++) {
			$pdf->AddPage();
			$tplIdx = $pdf->importPage($i);
			$pdf->useTemplate($tplIdx, 0, 0);
			$pdf->SetFont('Times', 'B', 70);
			$pdf->SetTextColor(192, 192, 192);
			$pdf->SetTextColor(255,192,203);
			$watermarkText = 'S A L I N A N';
			self::addWatermark(105, 220, $watermarkText, 45, $pdf);
			$pdf->SetXY(25, 25);
		}
	}

	static function addWatermark($x, $y, $watermarkText, $angle, $pdf){
		$angle = $angle * M_PI / 180;
		$c = cos($angle);
		$s = sin($angle);
		$cx = $x * 1;
		$cy = (300 - $y) * 1;
		$pdf->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, - $s, $c, $cx, $cy, - $cx, - $cy));
		$pdf->Text($x, $y, $watermarkText);
		$pdf->_out('Q');
	}
}