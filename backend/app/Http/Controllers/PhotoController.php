<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;

class PhotoController extends Controller
{
    // Upload a photo for a listing, create thumbnails and save URLs to listing.photos
    public function upload(Request $request, $id)
    {
        $request->validate(['file' => 'required|file|mimes:jpg,jpeg,png|max:5120']); // 5MB limit

        $listing = Listing::findOrFail($id);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $timestamp = time();
        $basename = "listing_{$id}_{$timestamp}";

        // store original
        $origPath = "listings/{$id}/{$basename}.{$ext}";
        $stored = Storage::disk('public')->putFileAs("listings/{$id}", $file, "{$basename}.{$ext}");

        // create thumbnails (400x300 and 150x100)
        try {
            $image = Image::make($file->getRealPath());

            $thumb1 = $image->resize(400, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($ext);

            $thumb1Path = "listings/{$id}/{$basename}_400x300.{$ext}";
            Storage::disk('public')->put($thumb1Path, (string)$thumb1);

            $thumb2 = $image->resize(150, 100, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($ext);

            $thumb2Path = "listings/{$id}/{$basename}_150x100.{$ext}";
            Storage::disk('public')->put($thumb2Path, (string)$thumb2);
        } catch (\Exception $e) {
            Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }

        // build URLs
        $origUrl = Storage::disk('public')->url($origPath);
        $t1Url = Storage::disk('public')->url($thumb1Path ?? $origPath);
        $t2Url = Storage::disk('public')->url($thumb2Path ?? $origPath);

        // append to listing photos
        $photos = $listing->photos ?? [];
        $photos[] = [
            'original' => $origUrl,
            'thumb_400x300' => $t1Url,
            'thumb_150x100' => $t2Url,
        ];

        $listing->photos = $photos;
        $listing->save();

        return response()->json(['message' => 'Uploaded', 'photo' => end($photos)], 201);
    }
}
