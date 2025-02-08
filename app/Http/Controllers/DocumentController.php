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
        $booking = Bookings::with('activity')->findOrFail($booking_id);
        
        $childrenPrice = $booking->children_qty * ($booking->activity->children_price ?? 0);
        $studentPrice = $booking->students_qty * ($booking->activity->student_price ?? 0);
        $adultPrice = $booking->adults_qty * ($booking->activity->adult_price ?? 0);
        $disabledPrice = $booking->disabled_qty * ($booking->activity->disabled_price ?? 0);
        $elderlyPrice = $booking->elderly_qty * ($booking->activity->elderly_price ?? 0);
        $monkPrice = $booking->monk_qty * ($booking->activity->monk_price ?? 0);
        $totalPrice = $childrenPrice + $studentPrice + $adultPrice + $disabledPrice + $elderlyPrice + $monkPrice;

        return view('emails.upload', compact('booking','totalPrice'));
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

        return back()->with('success', 'อัปโหลดเอกสารเรียบร้อยแล้ว');
    }
    public function destroy($document_id)
{
    $document = Documents::findOrFail($document_id);

    if (Storage::disk('public')->exists($document->file_path)) {
        Storage::disk('public')->delete($document->file_path);
    }

    $document->delete();

    return back()->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
}

}
