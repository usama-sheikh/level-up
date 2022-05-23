<?php
   
   namespace App\Http\Resources;
   
   use Illuminate\Http\Resources\Json\JsonResource;

   /**
    * @property mixed $message
    * @property mixed $source
    * @property mixed $id
    * @property mixed $author
    * @property mixed $audio
    * @property mixed $time
    */
   class CommentResource extends JsonResource
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
             'message' => $this->message,
             'source' => $this->source,
             'time' => $this->time,
             'author' => $this->author,
             'audio' => $this->audio,
         ];
      }
   }