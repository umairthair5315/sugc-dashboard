<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Get all members with their scores
    public function getMembers()
    {
        $members = DB::table('members')->get();
        
        $membersWithScores = [];
        
        foreach ($members as $member) {
            $scores = DB::table('scores')
                ->join('categories', 'scores.category_id', '=', 'categories.id')
                ->where('scores.member_id', $member->id)
                ->select('categories.name as category', 'scores.score')
                ->get();
            
            $memberData = [
                'id' => $member->id,
                'name' => $member->name . ' - ' . $member->roll_number,
                'role' => $member->role,
                'description' => $member->description,
                'profilePic' => $member->profile_pic,
                'scores' => []
            ];
            
            foreach ($scores as $score) {
                $memberData['scores'][$score->category] = $score->score;
            }
            
            $membersWithScores[] = $memberData;
        }
        
        return response()->json($membersWithScores);
    }
    
    // Get all categories
    public function getCategories()
    {
        $categories = DB::table('categories')->pluck('name');
        return response()->json($categories);
    }
    
    // Admin login
    public function adminLogin(Request $request)
    {
        $password = $request->input('password');
        $admin = DB::table('admins')->where('password', $password)->first();
        
        if ($admin) {
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 401);
    }
    
    // Update member score
    public function updateScore(Request $request)
    {
        $memberId = $request->input('member_id');
        $categoryName = $request->input('category');
        $score = $request->input('score');
        
        $category = DB::table('categories')->where('name', $categoryName)->first();
        
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
        
        $existingScore = DB::table('scores')
            ->where('member_id', $memberId)
            ->where('category_id', $category->id)
            ->first();
        
        if ($existingScore) {
            DB::table('scores')
                ->where('member_id', $memberId)
                ->where('category_id', $category->id)
                ->update(['score' => $score]);
        } else {
            DB::table('scores')->insert([
                'member_id' => $memberId,
                'category_id' => $category->id,
                'score' => $score
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    // Add new category
    public function addCategory(Request $request)
    {
        $categoryName = $request->input('name');
        
        $exists = DB::table('categories')->where('name', $categoryName)->exists();
        
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Category already exists'], 400);
        }
        
        DB::table('categories')->insert(['name' => $categoryName]);
        
        return response()->json(['success' => true]);
    }
    
    // Delete category
    public function deleteCategory(Request $request)
    {
        $categoryName = $request->input('name');
        
        DB::table('categories')->where('name', $categoryName)->delete();
        
        return response()->json(['success' => true]);
    }
    
    // Update member profile
    public function updateMemberProfile(Request $request)
    {
        $memberId = $request->input('member_id');
        $role = $request->input('role');
        $description = $request->input('description');
        $profilePic = $request->input('profile_pic');
        
        DB::table('members')
            ->where('id', $memberId)
            ->update([
                'role' => $role,
                'description' => $description,
                'profile_pic' => $profilePic
            ]);
        
        return response()->json(['success' => true]);
    }
}