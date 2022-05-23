<?php
   
   namespace App\Http\Controllers\Api;
   
   use App\Http\Resources\CommentResource;
   use Illuminate\Http\JsonResponse;
   use Illuminate\Http\Request;
   use App\Http\Requests\Api\Comment\StoreRequest;
   use App\Http\Controllers\Api\BaseController as BaseController;
   use App\Models\Audio;
   use App\Models\Comment;
   use Illuminate\Support\Facades\Auth;
   use Illuminate\Support\Facades\Validator;
   use App\Http\Resources\AudioResource;
   
   class CommentController extends BaseController
   {
      /**
       * Display a listing of the resource.
       *
       * @param AudioExistRequest $audio
       * @return JsonResponse
       */
      public function indexAll(AudioExistRequest $audio): JsonResponse
      {
         $audio = Audio::find((int)$audio_id);
         return [];
//         return $this->sendResponse(AudioResource::collection($audios), 'Audios retrieved successfully.');
      }
      /**
       * Display a listing of the resource.
       *
       * @param int $audio_id
       * @return JsonResponse
       */
      public function index(int $audio_id): JsonResponse
      {
         $audio = Audio::find($audio_id);
         
         if (!$audio)
            return $this->sendDefaultError('audio','Audio not found.');
   
         $comments = Comment::orderBy('id', 'desc')->where('audio_id', $audio->id)->get();
         
         return $this->sendResponse(CommentResource::collection($comments), 'Comments of a single audio retrieved successfully.');
      }
      /**
       * Store a newly created resource in storage.
       *
       * @param int $audio_id
       * @param StoreRequest $request
       * @return JsonResponse
       */
      public function store(int $audio_id, StoreRequest $request): JsonResponse
      {
         $audio = Audio::find($audio_id);
   
         if (!$audio)
            return $this->sendDefaultError('audio','Audio not found.');
   
         $input = $request->all();
         $input['user_id'] = Auth()->user()->id;
         $input['audio_id'] = $audio->id;
   
         $comment = Comment::create($input);
   
         if(!isset($input['message'])) {
            $source = $request->file('source');
            $comment->addMedia($source)->toMediaCollection(Comment::COMMENT_PATH, config('app.comment_disc'));
         }
         
         return $this->sendResponse(new CommentResource($comment), 'Comment stored successfully.');
      }
      
      /**
       * Display the specified resource.
       *
       * @param int $id
       * @return JsonResponse
       */
      public function show(int $id): JsonResponse
      {
         $audio = Audio::find($id);
         
         if (is_null($audio))
            return $this->sendDefaultError('audio','Audio not found.');
         
         return $this->sendResponse(new AudioResource($audio), 'Audio retrieved successfully.');
      }
   
      /**
       * Update the specified resource in storage.
       *
       * @param Request $request
       * @param Audio $audio
       * @return JsonResponse
       */
      public function update(Request $request, Audio $audio) : JsonResponse
      {
         $input = $request->all();
         
         $validator = Validator::make($input, [
             'name' => 'required',
             'detail' => 'required'
         ]);
         
         if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
         }
         
         $audio->name = $input['name'];
         $audio->detail = $input['detail'];
         $audio->save();
         
         return $this->sendResponse(new AudioResource($audio), 'Audio updated successfully.');
      }
   
      /**
       * Remove the specified resource from storage.
       *
       * @param int $audio_id
       * @param int $id
       * @return JsonResponse
       */
      public function destroy(int $audio_id, int $id) : JsonResponse
      {
         $audio = Audio::find($audio_id);
   
         if (!$audio)
            return $this->sendDefaultError('audio','Audio not found.');
   
         $comment = Comment::find($id);
         
         if (is_null($comment))
            return $this->sendDefaultError('comment','Comment not found.');
   
         if (auth()->user()->id !== $comment->user_id)
            return $this->sendDefaultError('comment','Comment belongs to someone else.');
   
         if ($audio->id !== $comment->audio_id)
            return $this->sendDefaultError('comment','Comment belongs to another audio.');
   
         $comment->clearMediaCollection(Comment::COMMENT_PATH);
         $comment->delete();
         
         return $this->sendResponse([], 'Comment deleted successfully.');
      }
   }