<?php

namespace Cristianhr\ExtBreadFieldsFix\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Controller;
use TCG\Voyager\Facades\Voyager;
use Cristianhr\ExtBreadFieldsFix\ContentTypes\MultipleImagesWithAttrsContentType;
use Cristianhr\ExtBreadFieldsFix\ContentTypes\KeyValueJsonContentType;

class ExtendedBreadFormFieldsFixController extends VoyagerBaseController
{

    public function getContentBasedOnType(Request $request, $slug, $row, $options = null)
    {
        switch ($row->type) {
            case 'key-value_to_json':
                return (new KeyValueJsonContentType($request, $slug, $row, $options))->handle();
            case 'multiple_images_with_attrs':
                return (new MultipleImagesWithAttrsContentType($request, $slug, $row, $options))->handle();
            default:
                return Controller::getContentBasedOnType($request, $slug, $row, $options);
        }
    }


    public function insertUpdateData($request, $slug, $rows, $data)
    {
        $fieldNameArr = [];
        $exFilesArr = [];
  
        foreach ($rows as $row) {
            if ($row->type == 'multiple_images_with_attrs') {
                $is_multiple_image_attrs = 1;             
                $fieldNameArr[] = $row->field;
                $exFilesArr[$row->field] = json_decode($data->{$row->field}, true);
                $request->except("{$row->field}");
            }
        }

        $new_data = VoyagerBaseController::insertUpdateData($request, $slug, $rows, $data);
                
        if (isset($is_multiple_image_attrs)) {
          foreach ($fieldNameArr as $field) {
            $end_content = [];
            foreach ($rows as $row) {
              $content = $new_data->{$field};
              if ($row->type == 'multiple_images_with_attrs' && !is_null($content) && $exFilesArr[$field] != json_decode($content, 1)) {
                if (isset($data->{$row->field})) {
                  if (!is_null($exFilesArr[$field])) {
                    $content = json_encode(array_merge($exFilesArr[$field], json_decode($content, 1)));
                  }
                }
                $new_content = $content;
              }
            }
              
            if (isset($new_content)) {
              $content = json_decode($new_content, 1);
            } else {
              $content = json_decode($content, 1);
            }

            if (isset($content)) {
              foreach ($content as $i => $value) {
                if (isset($request->{$field.'_ext'}[$i])) {
                  $end_content[] = array_merge($content[$i], $request->{$field.'_ext'}[$i]);
                } else {
                  $end_content[] = $content[$i];
                }
              }

              $data->{$field} = json_encode($end_content);			
            }
          }
            
          $data->save();
                    
          return $data;
        } else {
          return $new_data;
        }
    }
}
