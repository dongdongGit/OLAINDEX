@extends('mdui.layouts.admin')
@section('content')
<div class="mdui-container-fluid mdui-m-y-2">

    <div class="mdui-typo">
        <h1>密码设置</h1>
    </div>
    <form action="" method="post">
        @csrf
        <div class="mdui-textfield mdui-textfield-floating-label{{ $errors->has('old_password') ? ' mdui-textfield-invalid' : '' }}">
            <label class="mdui-textfield-label" for="old_password">旧密码</label>
            <input type="password" class="mdui-textfield-input" id="old_password" name="old_password" required>
            @if ($errors->has('old_password'))
            <div class="mdui-textfield-error">{{ $errors->first('old_password') }}</div>
            @endif
        </div>
        <br>
        <div class="mdui-textfield mdui-textfield-floating-label{{ $errors->has('password') ? ' mdui-textfield-invalid' : '' }}">
            <label class="mdui-textfield-label" for="password">新密码</label>
            <input type="password" class="mdui-textfield-input" id="password" name="password" required>
            @if ($errors->has('password'))
            <div class="mdui-textfield-error">{{ $errors->first('password') }}</div>
            @endif
        </div>
        <br>
        <div class="mdui-textfield mdui-textfield-floating-label{{ $errors->has('password_confirm') ? ' mdui-textfield-invalid' : '' }}">
            <label class="mdui-textfield-label" for="password_confirm">重复新密码</label>
            <input type="password" class="mdui-textfield-input" id="password_confirm" name="password_confirm" required>
            @if ($errors->has('password_confirm'))
            <div class="mdui-textfield-error">{{ $errors->first('password_confirm') }}</div>
            @endif
        </div>
        <br>

        <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="submit"><i
                class="mdui-icon material-icons">check</i> 保存
        </button>
    </form>
</div>
@stop