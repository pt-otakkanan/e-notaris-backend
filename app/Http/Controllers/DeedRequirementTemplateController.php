<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use App\Models\DeedRequirementTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeedRequirementTemplateController extends Controller
{
    // GET /deeds/{deed}/requirements
    public function index($deedId)
    {
        $deed = Deed::with('requirementTemplates')->find($deedId);
        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar template requirement berhasil diambil.',
            'data' => $deed->requirementTemplates
        ], 200);
    }

    // POST /deeds/{deed}/requirements
    public function store(Request $request, $deedId)
    {
        $deed = Deed::find($deedId);
        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'requirement_name' => 'required|string|max:255',
            'is_file_snapshot'  => 'sometimes|boolean',
            'is_active'        => 'sometimes|boolean',
            'requirement_id'   => 'sometimes|integer|nullable',
            'default_value'    => 'sometimes|string|nullable|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['deed_id'] = $deed->id;

        $template = DeedRequirementTemplate::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Template requirement berhasil dibuat.',
            'data' => $template
        ], 201);
    }

    // PUT /deeds/{deed}/requirements/{id}
    public function update(Request $request, $deedId, $id)
    {
        $template = DeedRequirementTemplate::where('deed_id', $deedId)->find($id);
        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'requirement_name' => 'sometimes|string|max:255',
            'is_file_snapshot'  => 'sometimes|boolean',
            'is_active'        => 'sometimes|boolean',
            'default_value'    => 'sometimes|string|nullable|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => $validator->errors()
            ], 422);
        }

        $template->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil diperbarui.',
            'data' => $template
        ], 200);
    }

    // DELETE /deeds/{deed}/requirements/{id}
    public function destroy($deedId, $id)
    {
        $template = DeedRequirementTemplate::where('deed_id', $deedId)->find($id);
        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan.',
            ], 404);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dihapus.',
        ], 200);
    }

    // POST /deeds/{deed}/requirements/{id}/toggle
    public function toggleActive($deedId, $id)
    {
        $template = DeedRequirementTemplate::where('deed_id', $deedId)->find($id);
        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan.',
            ], 404);
        }

        $template->is_active = !$template->is_active;
        $template->save();

        return response()->json([
            'success' => true,
            'message' => 'Status template diperbarui.',
            'data' => $template
        ], 200);
    }
}
