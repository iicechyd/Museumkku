<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documents;
use App\Models\Bookings;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function showUploadForm($booking_id)
    {
        $booking = Bookings::findOrFail($booking_id);
        return view('documents.upload', compact('booking'));
    }

    public function uploadDocument(Request $request, $booking_id)
    {
        $request->validate([
            'document' => 'required|mimes:pdf|max:2048',
        ]);

        $file = $request->file('document');

        $fileName = now()->format('Ymd_His') . '_' . $file->getClientOriginalName();

        $filePath = $file->store('documents', 'public');

        Documents::create([
            'booking_id' => $booking_id,
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        return redirect()->route('documents.upload', ['booking_id' => $booking_id])->with('success', 'เอกสารถูกอัปโหลดเรียบร้อยแล้ว');
    }
}
