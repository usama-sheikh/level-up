<?php
   
   namespace App\Http\Resources;
   
   use Illuminate\Http\Resources\Json\JsonResource;

   /**
    * @property mixed $id
    * @property mixed $surah
    * @property mixed $ayat
    * @property mixed $source
    * @property mixed $author
    * @property mixed $comments
    */
   class AudioResource extends JsonResource
   {
      /**
       * Transform the resource into an array.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return array
       */
      public function toArray($request)
      {
         return [
             'id' => $this->id,
             'surah' => $this->surah,
             'ayat' => $this->ayat,
             'source' => $this->source,
             'author' => $this->author,
             'comments' => $this->comments,
         ];
      }
   }