<?php
   
   namespace App\Http\Requests\Api\Audio;
   
   use App\Models\Audio;
   use Illuminate\Foundation\Http\FormRequest;
   
   /**
    * Class ApplyJobRequest
    */
   class StoreRequest extends FormRequest
   {
      /**
       * Determine if the user is authorized to make this request.
       *
       * @return bool
       */
      public function authorize(): bool
      {
         return true;
      }
      
      /**
       * Get the validation rules that apply to the request.
       *
       * @return array
       */
      public function rules(): array
      {
         return Audio::$rulesStore;
      }
   }
