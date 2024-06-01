<?php

namespace App\Http\Controllers\Be;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::with('users')->get();
        return redirect(route('admin'));
    }

    public function create()
    {
        $users = User::all(); // Lấy danh sách người dùng
        $currentUser = auth()->user(); // Lấy thông tin người dùng đăng nhập hiện tại
        return view('Back_End.unit.form', compact('users', 'currentUser'));
    }


    public function store(Request $request)
    {
        // Validate thông tin gửi lên từ form
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Tạo đơn vị mới
        $unit = new Unit();
        $unit->name = $request->name;
        $unit->description = $request->description;
        $unit->create_by = auth()->id();  // Người tạo là người dùng hiện tại
        $unit->save();  // Lưu đơn vị vào cơ sở dữ liệu

        // Kiểm tra và lưu các người dùng tham gia vào đơn vị
        if (!empty($request->user_ids)) {
            $unit->users()->sync($request->user_ids);
        }

        // Chuyển hướng người dùng với một thông báo thành công
        return redirect()->route('units.index')->with('success', 'Đơn vị mới đã được tạo thành công.');
    }

    public function show(Unit $unit)
    {
        $unit->load('users');
        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $unit->load('users');
        $users = User::all(); // Lấy danh sách người dùng để chỉnh sửa
        return view('units.edit', compact('unit', 'users'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'create_by' => 'required|integer|exists:users,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ]);

        $unit->update($request->only(['name', 'description', 'create_by']));
        $unit->users()->sync($request->users); // Cập nhật người dùng của đơn vị
        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->users()->detach(); // Xóa tất cả mối quan hệ người dùng trước khi xóa đơn vị
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }


    public function viewUsers($unitId)
    {
        $unit = Unit::with('users')->findOrFail($unitId);
        return view('Back_End.unit.manage-project-users', compact('unit'));
    }

    public function addUsers(Request $request, Unit $unit)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $unit->users()->attach($request->user_ids); // Thêm mối quan hệ giữa đơn vị và người dùng

        // return redirect()->back()->with('success', 'Thêm người dùng vào đơn vị thành công.')
        return redirect()->route('units.view-users', ['unit'=>$unit->id]);
    }

    public function createUserForm(Unit $unit)
    {
        $users = User::all(); // Lấy danh sách người dùng

        return view('Back_End.unit.create-user-to-unit', compact('unit', 'users'));
    }
    public function removeUserFromUnit(Unit $unit, $user_id)
    {
        $user = User::findOrFail($user_id);

        // Kiểm tra xem $unit và $user có tồn tại và có liên kết với nhau

        // Xóa người dùng khỏi $unit
        $unit->users()->detach($user);
        return redirect()->back()->with('success', 'Thêm người dùng vào đơn vị thành công.');

        // return redirect()->route('units.view-users');

        // Thực hiện các thao tác khác sau khi xóa người dùng thành công

        // Redirect hoặc trả về view tùy theo yêu cầu của bạn
    }
}
