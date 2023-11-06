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
    public function downloadWithWatermark(int $id): DataResponse {
		// initiate FPDI
		$pdf = new Fpdi();
		$a = \OC::$server->getRootFolder()->getById($id);
		$file = $a[0];
		// $fileInput = '/var/www/html/data' . $file->getPath();
		// var_dump($fileInput);
		if ($file instanceof File) {
			// header("Content-Type: application/octet-stream"); 			
			// header("Content-Disposition: attachment; filename=" . "ANO.pdf");    
			// header("Content-Type: application/download"); 
			// header("Content-Description: File Transfer");             
			// header("Content-Length: " . filesize($file)); 
			
			$newfname = 'tempFile'.date("Y-m-d h:m:s").'.pdf';
			$source = $file->fopen('rb');
			$newf = fopen ("/var/www/html/data/" . $newfname, 'wb');
			// $pages_count = $pdf->setSourceFile(new StreamReader($source));
			while (!feof($source)) { 
				fwrite($newf, fread($source, 65536));
				// echo fread($source, 65536); 
				flush(); // This is essential for large downloads 
			}  
			fclose($source);
			if ($newf) {
				fclose($newf);
			}

			$file = fopen("/var/www/html/data/" . $newfname, 'rb');
			if($file) {
				// $newPdfF = fopen("_data/$pdfName", 'wb');
				$pdf = new Fpdi();
				$pageCount = $pdf->setSourceFile($file);
				// if($newPdfF) {
				// 	// Now let us use this file to try and remove the bottom logos.
					
				// }
			}

		}
		// $path = $file->getPath();
		// var_dump($source);
    }
}