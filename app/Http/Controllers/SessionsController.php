<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//use App\Http\Requests;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 登录页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * 创建新会话（登录）
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            if (Auth::user()->activated) {//如果激活状态为true，就允许登录
                // 登录成功后的相关操作
                session()->flash('success', '欢迎回来!');
                return redirect()->intended(route('users.show', [Auth::user()]));//用户登录之后使用intended可以跳转到用户登录之前想跳转的页面
            } else {//否则就去邮箱激活
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
        } else {
            // 登录失败后的相关操作
            session()->flash('danger', '很抱歉，您的邮箱与密码不匹配');
            return redirect()->back();
        }
    }

    /**
     * 销毁会话（退出登录）
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }


}
