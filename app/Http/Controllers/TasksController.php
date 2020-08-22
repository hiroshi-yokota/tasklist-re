<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            $tasks = Task::where('user_id', $user->id)->get();
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }

        // Welcomeビューでそれらを表示
        return view('welcome', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        if (\Auth::check()) { // 認証済みの場合
            $task = new Task;

            // メッセージ作成ビューを表示
            return view('tasks.create', [
                'task' => $task,
            ]);
        }else{
            return redirect('/');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        $user = \Auth::user();
        // メッセージを作成
        $task = new Task;
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->user_id = $user->id;    // 追加
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        if (\Auth::check()) { // 認証済みの場合
            $user = \Auth::user();
            $task = Task::findOrFail($id);
            // メッセージ詳細ビューでそれを表示
            if($task->user_id == $user->id){
                return view('tasks.show', [
                  'task' => $task,
                ]);
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        if (\Auth::check()) { // 認証済みの場合
            $user = \Auth::user();
            $task = Task::findOrFail($id);
            if($task->user_id == $user->id){
                // メッセージ編集ビューでそれを表示
                return view('tasks.edit', [
                    'task' => $task,
                ]);
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        if (\Auth::check()) { // 認証済みの場合
            $user = \Auth::user();
            // バリデーション
            $request->validate([
                'status' => 'required|max:10',   // 追加
                'content' => 'required|max:255',
            ]);
            // idの値でメッセージを検索して取得
            $task = Task::findOrFail($id);
            if($task->user_id == $user->id){
                // メッセージ編集ビューでそれを表示
                // メッセージを更新
                $task->status = $request->status;    // 追加
                $task->content = $request->content;
                $task->save();
                return redirect('/');
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // deleteでtasks/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        if (\Auth::check()) { // 認証済みの場合
            $user = \Auth::user();
            // idの値でメッセージを検索して取得
            $task = Task::findOrFail($id);
            if($task->user_id == $user->id){
                // メッセージを削除
                $task->delete();
                return redirect('/');
            }else{
                return redirect('/');
            }
        }else{
            return redirect('/');
        }
    }
}
