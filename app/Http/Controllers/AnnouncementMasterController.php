<?php

namespace App\Http\Controllers;

use App\Services\AnnouncementMasterService;
use App\Services\AnnouncementMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AnnouncementMasterController extends Controller
{
    protected $announcementMasterService;
    protected $announcementMediaService;

    public function __construct(
        AnnouncementMasterService $announcementMasterService,
        AnnouncementMediaService $announcementMediaService
    ) {
        $this->announcementMasterService = $announcementMasterService;
        $this->announcementMediaService = $announcementMediaService;

        $this->middleware('auth');
        $this->middleware(
            'permission:announcement-master-list|announcement-master-create|announcement-master-edit|announcement-master-delete',
            ['only' => ['index', 'ajaxList']]
        );
        $this->middleware('permission:announcement-master-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:announcement-master-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:announcement-master-delete', ['only' => ['destroy']]);
    }

    /**
     * Admin list page
     */
    public function index()
    {
        $data['menu'] = "Announcement Master";
        $data['user'] = auth()->user();
        return view("announcement_master/index", $data);
    }

    /**
     * AJAX list for pagination
     */
    public function ajaxList()
    {
        $data['query'] = $this->announcementMasterService->getData();
        return view("announcement_master.ajax_list", $data);
    }

    /**
     * Store new announcement
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|max:255',
                'description' => 'required',
                'media.*' => 'max:512000'
            ],
            [
                'media.*.max' => 'The media file size must not exceed 500MB.',
                'description.required' => 'The message field is required.'
            ]
        );

        // Custom validation for media file types - add to validator errors
        $mediaError = null;
        if ($request->hasFile('media')) {
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'mp4', 'mov', 'avi'];
            foreach ($request->file('media') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                if (!in_array($extension, $allowedExtensions)) {
                    $mediaError = 'The media must be a file of type: jpeg, jpg, png, gif, mp4, mov, avi.';
                    break;
                }
            }
        }

        // Return all errors together
        if ($validator->fails() || $mediaError) {
            $errors = $validator->errors()->toArray();
            if ($mediaError) {
                $errors['media'] = [$mediaError];
            }
            return response()->json([
                'status' => false,
                'msg' => 'Please fix the validation errors.',
                'error' => $errors
            ]);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'steps_summary' => $request->steps_summary,
            'is_published' => '0'
        ];

        $announcement = $this->announcementMasterService->save($data);

        // Handle media uploads
        if ($request->hasFile('media')) {
            $sortOrder = 0;
            foreach ($request->file('media') as $file) {
                $mediaType = strpos($file->getMimeType(), 'video') !== false ? 'video' : 'photo';
                $fileName = uniqid() . time() . '.' . $file->getClientOriginalExtension();

                // Upload file
                if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                    $destination = public_path('announcement-media');
                    if (!file_exists($destination)) {
                        mkdir($destination, 0777, true);
                    }
                    $file->move($destination, $fileName);
                    $filePath = 'announcement-media/' . $fileName;
                } else {
                    Storage::disk('s3')->putFileAs('announcement-media', $file, $fileName);
                    $filePath = 'announcement-media/' . $fileName;
                }

                // Save media record
                $this->announcementMediaService->save([
                    'announcement_id' => $announcement->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'media_type' => $mediaType,
                    'sort_order' => $sortOrder++
                ]);
            }
        }

        return response()->json(['status' => true, 'msg' => 'Announcement created successfully', 'data' => $announcement]);
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|max:255',
                'description' => 'required',
                'media.*' => 'max:512000'
            ],
            [
                'media.*.max' => 'The media file size must not exceed 500MB.',
                'description.required' => 'The message field is required.'
            ]
        );

        // Custom validation for media file types - add to validator errors
        $mediaError = null;
        if ($request->hasFile('media')) {
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'mp4', 'mov', 'avi'];
            foreach ($request->file('media') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                if (!in_array($extension, $allowedExtensions)) {
                    $mediaError = 'The media must be a file of type: jpeg, jpg, png, gif, mp4, mov, avi.';
                    break;
                }
            }
        }

        // Return all errors together
        if ($validator->fails() || $mediaError) {
            $errors = $validator->errors()->toArray();
            if ($mediaError) {
                $errors['media'] = [$mediaError];
            }
            return response()->json([
                'status' => false,
                'msg' => 'Please fix the validation errors.',
                'error' => $errors
            ]);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'steps_summary' => $request->steps_summary
        ];

        $this->announcementMasterService->update($data, ['id' => $id]);

        // Handle new media uploads
        if ($request->hasFile('media')) {
            $existingMedia = $this->announcementMediaService->getByAnnouncementId($id);
            $sortOrder = count($existingMedia);

            foreach ($request->file('media') as $file) {
                $mediaType = strpos($file->getMimeType(), 'video') !== false ? 'video' : 'photo';
                $fileName = uniqid() . time() . '.' . $file->getClientOriginalExtension();

                if (env('FILE_UPLOAD_PERMISSION') == 'development') {
                    $destination = public_path('announcement-media');
                    if (!file_exists($destination)) {
                        mkdir($destination, 0777, true);
                    }
                    $file->move($destination, $fileName);
                    $filePath = 'announcement-media/' . $fileName;
                } else {
                    Storage::disk('s3')->putFileAs('announcement-media', $file, $fileName);
                    $filePath = 'announcement-media/' . $fileName;
                }

                $this->announcementMediaService->save([
                    'announcement_id' => $id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'media_type' => $mediaType,
                    'sort_order' => $sortOrder++
                ]);
            }
        }

        return response()->json(['status' => true, 'msg' => 'Announcement updated successfully']);
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        $result = $this->announcementMasterService->delete($id);

        if ($result) {
            return response()->json(['status' => true, 'msg' => 'Announcement deleted successfully']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
        }
    }

    /**
     * Publish announcement
     */
    public function publish($id)
    {
        $result = $this->announcementMasterService->publish($id);

        if ($result) {
            return response()->json(['status' => true, 'msg' => 'Announcement published successfully']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Failed to publish announcement']);
        }
    }

    /**
     * Delete media file
     */
    public function deleteMedia($id)
    {
        $result = $this->announcementMediaService->delete($id);

        if ($result) {
            return response()->json(['status' => true, 'msg' => 'Media deleted successfully']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Failed to delete media']);
        }
    }

    /**
     * Get announcement details (for edit modal)
     */
    public function show($id)
    {
        $announcement = $this->announcementMasterService->getDetailById($id);

        if ($announcement) {
            return response()->json(['status' => true, 'data' => $announcement]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Announcement not found']);
        }
    }

    /**
     * Show/Download media image or video - returns temporary URL for S3
     */
    public function showImage($id)
    {
        $media = $this->announcementMediaService->getById($id);

        if (!$media) {
            abort(404);
        }

        $filePath = $media->file_path;

        // Check if file exists locally first
        $localFile = public_path($filePath);

        if (env('FILE_UPLOAD_PERMISSION') == 'development') {
            if (file_exists($localFile)) {
                return response()->file($localFile);
            } else {
                abort(404);
            }
        } else {
            // Try local first, then S3
            if (file_exists($localFile)) {
                return response()->file($localFile);
            } else {
                // Generate temporary URL from S3 and redirect
                if (str_contains($filePath, 'announcement-media')) {
                    $url = Storage::disk('s3')->temporaryUrl($filePath, now()->addMinutes(60));
                } else {
                    $url = Storage::disk('s3')->temporaryUrl('announcement-media/' . $media->file_name, now()->addMinutes(60));
                }
                return redirect($url);
            }
        }
    }
}
