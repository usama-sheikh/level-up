<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use FFMpeg\FFMpeg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Audio\StoreRequest;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Audio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AudioResource;

class AudioController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function indexAll(): JsonResponse
    {
        $audios = Audio::orderBy('id', 'desc')->get();

        return $this->sendResponse(AudioResource::collection($audios), 'Audios retrieved successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $audios = Audio::orderBy('id', 'desc')->where('user_id', Auth::user()->id)->get();

        return $this->sendResponse(AudioResource::collection($audios), 'Audios of a single user retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['user_id'] = Auth()->user()->id;

        $audio = Audio::create($input);


        $source = $request->file('source');

//        $randomName = uniqid();
//        $request->source->storeAs('public/temp/', $randomName . '.mp4');


//        $tempVideo = FFMpeg::fromDisk('public')->open('/temp/' . $randomName . '.mp4');
//        $audioFormat = new FFMpeg\Format\Audio\Mp3();
//        $tempVideo->save($audioFormat, public_path() . '/temp/' . $randomName . '.mp3');

//        if (file_exists(public_path() . '/temp/' . $randomName . '.mp4')) {
//            \File::delete(public_path() . '/temp/' . $randomName . '.mp4');
//        }

//        $convertedSource = FFMpeg::open(public_path()."/temp/$randomName.mp4")
//            ->export()
//            ->inFormat(new \FFMpeg\Format\Audio\Mp3)
//            ->save(public_path()."/temp/$randomName.mp3");

        $audio->addMedia($source)->toMediaCollection(Audio::AUDIO_PATH, config('app.audio_disc'));

        return $this->sendResponse(new AudioResource($audio), 'Audio created successfully.');
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
            return $this->sendDefaultError('audio', 'Audio not found.');

        return $this->sendResponse(new AudioResource($audio), 'Audio retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Audio $audio
     * @return JsonResponse
     */
    public function update(Request $request, Audio $audio): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
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
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $audio = Audio::find($id);

        if (is_null($audio))
            return $this->sendDefaultError('audio', 'Audio not found.');

        if (auth()->user()->id !== $audio->user_id)
            return $this->sendDefaultError('audio', 'Audio belongs to someone else.');


        $comments = Comment::where('audio_id', $audio->id)->get();
        foreach ($comments as $comment) {
            $comment->clearMediaCollection(Comment::COMMENT_PATH);
            $comment->delete();
        }

        $audio->clearMediaCollection(Audio::AUDIO_PATH);
        $audio->delete();


        return $this->sendResponse([], 'Audio deleted successfully.');
    }
}
