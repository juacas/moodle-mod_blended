<?php
/*
 * Copyright (C) 2015 juacas
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of blendedtcp
 *
 * @author juacas
 */
class blendedPDF extends TCPDF {
    	/**
		* Puts an image in the page. 
		* The upper-left corner must be given. 
		* The dimensions can be specified in different ways:<ul>
		* <li>explicit width and height (expressed in user unit)</li>
		* <li>one explicit dimension, the other being calculated automatically in order to keep the original proportions</li>
		* <li>no explicit dimension, in which case the image is put at 72 dpi</li></ul>
		* Supported formats are JPEG and PNG images whitout GD library and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;
		* The format can be specified explicitly or inferred from the file extension.<br />
		* It is possible to put a link on the image.<br />
		* Remark: if an image is used several times, only one copy will be embedded in the file.<br />
		* @param string $file Name of the file containing the image.
		* @param float $x Abscissa of the upper-left corner.
		* @param float $y Ordinate of the upper-left corner.
		* @param float $w Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param float $h Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param string $type Image format. Possible values are (case insensitive): JPEG and PNG (whitout GD library) and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;. If not specified, the type is inferred from the file extension.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param string $align Indicates the alignment of the pointer next to image insertion relative to image height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		* @param boolean $resize If true resize (reduce) the image to fit $w and $h (requires GD library).
		* @param int $dpi dot-per-inch resolution used on resize
		* 
		* @return image information
		* @since 1.1
		*/
		 public function writeBlended2DBarcode($code, $type='',$x='', $y='', $w=0, $h=0,   $align='', $resize=false, $dpi=300,  $link='') 
		 {
		 	require_once('QR/qr_lib.php');
		 	
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			
			// check if image has been already added on document
			if (!in_array($code, $this->imagekeys)) {
				//First use of image, get info
				
				
				
				//JPC 
				$img=generateQR($code);
				// get image size
				$pixh=imagesy($img);
				$pixw=imagesx($img);
			
			
			// calculate image width and height on document
			if (($w <= 0) AND ($h <= 0)) {
				// convert image size to document unit
				$w = $pixw / ($this->imgscale * $this->k);
				$h = $pixh / ($this->imgscale * $this->k);
			} elseif ($w <= 0) {
				$w = $h * $pixw / $pixh;
			} elseif ($h <= 0) {
				$h = $w * $pixh / $pixw;
			}
			// calculate new minimum dimensions in pixels
			$neww = round($w * $this->k * $dpi / $this->dpi);
			$newh = round($h * $this->k * $dpi / $this->dpi);
			// check if resize is necessary (resize is used only to reduce the image)
			if (($neww * $newh) >= ($pixw * $pixh)) {
				$resize = false;
			}
				if ($resize) {
							$imgr = imagecreatetruecolor($neww, $newh);
							imagecopyresampled($imgr, $img, 0, 0, 0, 0, $neww, $newh, $pixw, $pixh); 
							$info = $this->_toJPEG($imgr);
						} else {
							$info = $this->_toJPEG($img);
						}
						
			
				$info['i'] = $this->numimages + 1;
				
				// add image to document cache $code is the key
				$this->setImageBuffer($code, $info);
			} else {
                            $info = $this->getImageBuffer($code);
                      
			}
			// Check whether we need a new page first as this does not fit
			if ((($y + $h) > $this->PageBreakTrigger) AND (!$this->InFooter) AND $this->AcceptPageBreak()) {
				// Automatic page break
				$this->AddPage($this->CurOrientation);
				// Reset Y coordinate to the top of next page
				$y = $this->GetY() + $this->cMargin;
			}
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			// set alignment
			if ($this->rtl) {
				
					$ximg = $this->w - $x - $w;
					// set left side coordinate
					$this->img_rb_x = $ximg;
				
			} else {
				
					$ximg = $x;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				
			}
			
				$xkimg = $ximg * $this->k;
			
			$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', ($w * $this->k), ($h * $this->k), $xkimg, (($this->h - ($y + $h)) * $this->k), $info['i']));
			if ($link) {
				$this->Link($ximg, $y, $w, $h, $link, 0);
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T': {
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M': {
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B': {
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N': {
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
			$this->endlinex = $this->img_rb_x;
			return $info['i'];
		}
                /**
		* Convert the loaded php image to a JPEG and then return a structure for the PDF creator.
		* This function requires GD library and write access to the directory defined on K_PATH_CACHE constant.
		* @param string $file Image file name.
		* @param image $image Image object.
		* return image JPEG image object.
		* @access protected
		*/
		protected function _toJPEG($image) {
			$tempname = tempnam(K_PATH_CACHE, 'jpg_');
			imagejpeg($image, $tempname, $this->jpeg_quality);
			imagedestroy($image);
			$retvars = $this->_parsejpeg($tempname);
			// tidy up by removing temporary image
			unlink($tempname);
			return $retvars;
		}
		
		/**
		* Extract info from a JPEG file without using the GD library.
		* @param string $file image file to parse
		* @return array structure containing the image data
		* @access protected
		*/
		protected function _parsejpeg($file) {
			$a = getimagesize($file);
			if (empty($a)) {
				$this->Error('Missing or incorrect image file: '.$file);
			}
			if ($a[2] != 2) {
				$this->Error('Not a JPEG file: '.$file);
			}
			if ((!isset($a['channels'])) OR ($a['channels'] == 3)) {
				$colspace = 'DeviceRGB';
			} elseif ($a['channels'] == 4) {
				$colspace = 'DeviceCMYK';
			} else {
				$colspace = 'DeviceGray';
			}
			$bpc = isset($a['bits']) ? $a['bits'] : 8;
			$data = file_get_contents($file);
			return array('w' => $a[0], 'h' => $a[1], 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'DCTDecode', 'data' => $data);
		}
}
