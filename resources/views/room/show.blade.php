@extends('layouts.html')

@section('content')
<div class="container">
    <el-row>
        <el-col :span="24">
            <div class="grid-content bg-purple-dark">
                <room :room="{{ $room }}"
                      :user="{{ $user }}">
                </room>
            </div>
        </el-col>
    </el-row>
</div>
@endsection
