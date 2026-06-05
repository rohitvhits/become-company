<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class AwsHelper
{
	public static function getImagesFromAWS($local_folder_path, $aws_path, $image)
	{
		if(env('FILE_UPLOAD_PERMISSION')  != 'development') {
			$path = trim($aws_path, '/') . '/' . $image;
			if (!Storage::disk('s3')->exists($path)) {
				$file = public_path('allupload/default.png');
				$mime = mime_content_type($file);
				return response(file_get_contents($file), 200)
					->header('Content-Type', $mime)
					->header('Content-Disposition', 'inline; filename="'.$image.'"')
					->header('Cache-Control', 'public, max-age=86400');
			}
			return redirect(Storage::disk('s3')->temporaryUrl($path,now()->addMinutes(60)));
		}else{
			$file = public_path($local_folder_path . DIRECTORY_SEPARATOR . $image);
			if (file_exists($file)) {
				$mime = mime_content_type($file);
				return response(file_get_contents($file), 200)
					->header('Content-Type', $mime)
					->header('Content-Disposition', 'inline; filename="'.$image.'"')
					->header('Cache-Control', 'public, max-age=86400');
			}
		}
	}

	public static function commonGetImagesFromAws($path,$fileName){
		if(env('FILE_UPLOAD_PERMISSION')  != 'development') {
			return redirect(Storage::disk('s3')->temporaryUrl($path.'/'.$fileName,now()->addMinutes(10)));
		}else{
			$file = public_path('/').$path;
			if (file_exists($file)) {
				$mime = mime_content_type($file);
				return response(file_get_contents($file), 200)
					->header('Content-Type', $mime)
					->header('Content-Disposition', 'inline; filename="'.$fileName.'"')
					->header('Cache-Control', 'public, max-age=86400');
			}
		}
	}
}