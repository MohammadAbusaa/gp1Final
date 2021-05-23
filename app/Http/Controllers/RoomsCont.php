<?php

namespace App\Http\Controllers;

use App\Events\PostSent;
use App\Events\StudentJoined;
use App\Models\Answer;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\File;
use App\Models\Material;
use App\Models\Post;
use App\Models\Question;
use App\Models\Room;
use App\Models\User;
use App\Models\Student;
use App\Models\Notification;
use App\Models\StudentAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomsCont extends Controller
{
    public function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getStudentRooms(Request $request)
    {
        $rooms = $request->user()->student->rooms;
        $arr = [];
        foreach ($rooms as $item) {
            array_push($arr, [
                'name' => $item['name'],
                'room_id' => $item['pivot']->room_id,
                'type' => $item['type'],
            ]);
        }
        return response()->json(['rooms' => $arr]);
    }

    public function create(Request $request)
    {
        $room = new Room($request->validate([
            'name' => 'required',
            'class' => 'required|in:one,two,three,four,five,six,seven',
            'subject' => 'required',
            'type' => 'required|in:public,private',
        ]));
        $room->name = $request->name;
        $room->class = $request->class;
        $room->subject = $request->subject;
        $room->teacher_id = $request->user()->id;
        if ($request->type == 'public') {
            $room->type = 1;
        } else {
            $room->type = 0;
        }
        $room->password = $this->generateRandomString(20);

        $room->save();

        return response('saved');

    }

    public function getRoomInfo(Request $request, $id)
    {
        $room = Room::find($id);
        $info = [
            'roomInf' => [
                'name' => $room->name,
                'teacher' => $room->teacher->user->name,
                'subject' => $room->subject,
                'type' => $room->type,
                'password' => $room->password,
                'user'=>$request->user()->name,
            ],

        ];
        return response()->json($info);
    }

    public function getRoomPosts(Request $request, $id)
    {
        $room = Room::find($id);
        //dd($room->name);
        $p = $room->posts;
        $posts = [];
        foreach ($p as $k => $v) {
            array_push($posts, [
                'body' => $v->body,
                'id' => $v->id,
                'user' => User::find($v->user_id)->name,
                'time' => $v->created_at,
            ]);
        }
        return response()->json(['posts' => $posts]);
    }

    public function storePost(Request $request, $id)
    {
        $post = new Post($request->validate([
            'post' => 'required',
        ]));
        $post->body = $request->post;
        $post->user_id = $request->user()->id;
        $post->room_id = $id;
        $post->save();
        //dd(\broadcast(new PostSent($request->user(),$post))->toOthers());
        //\broadcast(new PostSent($request->user(), $post))->toOthers();
        if($request->important==true){
            $notification=new Notification();
            $notification->body=Room::find($id)->name.' قمت المعلم بانشاء منشور مهم بالغرفة الصفية';
            $notification->user_id=$request->user()->id;
            $notification->seen=false;
    
            $notification->save();
        }

        return response('added');
    }

    public function addStudentToRoom(Request $request, $id)
    {
        //dd($request->user());
        $room=Room::find($id);
        if($room->type===0&&$request->password!==$room->password){
            return response('password is not correct',422);
        }
        $request->user()->student->rooms()->attach($id);

        $notification=new Notification();
        $notification->body=$room->name.' قمت بالتسجيل بالغرفة الصفية';
        $notification->user_id=$request->user()->id;
        $notification->seen=false;

        $notification->save();

        return response('added');
    }

    public function searchForRooms(Request $request)
    {
        // split on 1+ whitespace & ignore empty (eg. trailing space)
        $searchValues = preg_split('/\s+/', $request->name, -1, PREG_SPLIT_NO_EMPTY);

        $rooms = Room::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->orWhere('class', $request->class)->orWhere('subject', $request->subject)->get();

        $user_rooms = $request->user()->student->rooms->map->only(['name', 'id','type']);
        $curr_rooms = $rooms->map->only(['name', 'id','type']);
        //dd($curr_rooms);

        return response()->json($this->compare($user_rooms, $curr_rooms));
    }

    private function compare($coll1, $coll2)
    {
        $flag = false;
        $arr = [];
        foreach ($coll2 as $item) {
            $flag = false;
            foreach ($coll1 as $item2) {
                if ($item['name'] == $item2['name']) {
                    $flag = true;
                    break;
                }
            }
            if (!$flag) {
                $temp=$item+array('teacher'=>Room::find($item['id'])->teacher->user->name);
                array_push($arr, $temp);
            }
        }
        return $arr;
    }

    public function getRoomAssignments(Request $request, $id)
    {
        $room = Room::find($id);
        $a = $room->assignments;
        $assignments = $a->map->only(['id', 'assignment_details', 'due_date']);
        return response()->json(['assignments' => $assignments]);
    }

    public function getTeacherRooms(Request $request)
    {
        $rooms = $request->user()->teacher->rooms->map->only(['id', 'name', 'type']);
        return response()->json($rooms);
    }

    public function storeHW(Request $request, $id)
    {
        $data = $request->validate([
            'dueDate' => 'required',
            'hwDesc' => 'required',
        ]);

        if ($request->hasFile('hwFile') && $request->hwFile->isValid()) {
            $f = new File();
            $a = new Assignment();

            $file = $request->file('hwFile');

            $a->name = 'assign'.$id;
            $a->assignment_details = $data['hwDesc'];
            $a->start_date = Carbon::now();
            $a->due_date = Carbon::create($data['dueDate']);
            $a->teacher_id = $request->user()->id;
            $a->room_id = $id;
            $a->save();

            $path = Storage::putFile('/uploads', $file);
            $f->name = $file->getClientOriginalName();
            $f->path = base_path() . '/storage/app/' . $path;
            $f->user_id = $request->user()->id;
            $f->size = $file->getSize();
            $f->ext = $request->hwFile->getClientOriginalExtension();
            $f->save();

            $a->assignment_file = $f->id;
            $a->save();

            return response('added');
        } else if (!$request->hasFile('hwFile')) {
            $a = new Assignment();

            $a->name = 'assign' . $request->user()->id;
            $a->assignment_details = $data['hwDesc'];
            $a->start_date = Carbon::now();
            $a->due_date = Carbon::create($data['dueDate']);
            $a->teacher_id = $request->user()->id;
            $a->room_id = $id;
            $a->save();

            return response('added');
        }
        return response('failed')->setStatusCode(422);
    }
    public function getHandedTasks(Request $request, $id)
    {
        $a = Assignment::find($id)->students;
        $info = [];
        foreach ($a as $v) {
            array_push($info, [
                'handed_date' => $v['pivot']->haded_date,
                'StudentName' => $v->user->name,
                'file_id' => $v['pivot']->handed_file,
                'id'=>$v->id,
                'mark'=>$v['pivot']->mark,
                'feedback'=>$v['pivot']->feedback,
            ]);
        }
        return response()->json(['info' => $info]);
    }

    public function downloadFile($id)
    {
        $f=File::find($id);
        return response()->download($f->path,$f->name,['Content-Disposition'=>'attachment']);
    }

    public function deletePost($id)
    {
        Post::destroy($id);
        return response('done');
    }

    public function getRoomUsers($id)
    {
        $s = Room::find($id)->students;
        $users = [];
        foreach ($s as $v) {
            array_push($users, [
                'name' => $v->user->name,
                'id' => $v->user->id,
            ]);
        }
        return response()->json(['users' => $users]);
    }

    public function sendCirc(Request $request, $id)
    {
        //dd($request->all());
        $request->validate([
            'circFile' => 'file',
            'circDesc' => 'required',
        ]);

        if ($request->circFile->isValid()) {
            $path = Storage::putFile('/uploads', $request->circFile);

            $file = new File();
            $mat = new Material();

            $file->name = $request->circFile->getClientOriginalName();
            $file->ext = $request->circFile->getClientOriginalExtension();
            $file->path = base_path() . '/storage/app/' . $path;
            $file->user_id = $request->user()->id;
            $file->size = $request->circFile->getSize();
            $file->save();

            $mat->description = $request->circDesc;
            $mat->file_id = $file->id;
            $mat->room_id = $id;
            $mat->save();

            return \response('done');
        }
        return response('failed');
    }

    public function getCirc($id)
    {
        return response()->json(['circ' => Room::find($id)->materials->map->only(['id', 'file_id', 'description'])]);
    }

    public function updateRoomPassword(Request $request, $id)
    {
        $pass = $request->validate(['password' => 'required']);
        Room::find($id)->update(['password' => $request->password]);
        return response(['status' => 'OK']);
    }

    public function updateRoomName(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        Room::find($id)->update(['name' => $request->name]);
        return response(['status' => 'OK']);
    }

    public function updateRoomType(Request $request, $id)
    {
        $room = Room::find($id);
        if ($room->type == 1) {
            $room->update(['type' => '0']);
        } else {
            $room->update(['type' => '1']);
        }

        return response(['status' => 'OK']);
    }

    public function deleteRoom(Request $request,$id)
    {
        $room=Room::find($id)->name;
        Room::destroy($id);

        $notification=new Notification();
        $notification->body=$room.' قمت بحذف الغرفة الصفية';
        $notification->user_id=$request->user()->id;
        $notification->seen=false;

        $notification->save();

        return \response(['status' => 'OK']);
    }

    public function deleteStudentFromRoom(Request $request,$room_id, $student_id)
    {
        $stuname=Student::find($student_id)->user->name;
        Room::find($room_id)->students->find($student_id)->delete();

        $notification=new Notification();
        $notification->body=$room->name.' من الغرفة الصفية '.$stuname.' قمت بحذف الطالب ';
        $notification->user_id=$request->user()->id;
        $notification->seen=false;

        $notification->save();

        $notification=new Notification();
        $notification->body=$room->name.' تم طردك من الغرفة الصفية ';
        $notification->user_id=$student_id;
        $notification->seen=false;

        $notification->save();

        return response(['status' => 'OK']);
    }

    public function sendExam(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'time' => 'required', // REMEMBER TO CHECK DATE ITSELF
        ]);
        $exam = new \App\Models\Exam();
        $exam->name = $data['name'];
        $exam->room_id = $id;
        $exam->start_date = $data['time'];
        $exam->is_enabled = 0;
        $exam->save();

        $room=Room::find($id);
        $stu=Room::find($id)->students;

        foreach($stu as $v){
            $notification=new Notification();
            $notification->body=$room->name.' قام المعلم برفع واجب جديد في الغرفة الصفية ';
            $notification->user_id=$v->id;
            $notification->seen=false;
    
            $notification->save();
        }

        return \response()->json(['status'=>'OK']);
    }

    public function getStudentMarks($sid,$eid)
    {
        $mark=0;
        $ans=StudentAnswer::where('student_id',$sid)->where('exam_id',$eid)->get();

        if($ans->isEmpty())return null;
        
        foreach($ans as $v){
            $correct=Answer::where('question_id',$v['question_id'])->where('is_correct',true)->first();
            if($correct['answer']===$v['body'])$mark++;
        }

        return ['mark'=>$mark,'all'=>Exam::find($eid)->questions->count(),];
    }

    public function updateExamType($id)
    {
        $exam=Exam::find($id);
        if($exam->disabled==false){
            $exam->update(['disabled'=>1]);

            $notification=new Notification();
            $notification->body=$exam->room->name.' قام المعلم برفع امتحان جديد في الغرفة';
            $notification->user_id=$request->user()->id;
            $notification->seen=false;
    
            $notification->save();
        }
        else {
            $exam->update(['disabled'=>0]);
        }
    }

    public function getRoomExams(Request $request,$id)
    {
        $exams = Room::find($id)->exams->map->only(['id', 'name', 'start_date', 'is_enabled']);
        $arr = [];
        foreach ($exams as $value) {
            $qs = Question::where('exam_id', $value['id']);
            $temp = $value + array('noOfQuestions' => $qs->count());
            $temp+=array('enabled'=>$value['is_enabled']);
            $temp+=array('start_date'=>$value['start_date']);
            if(!is_null($request->user()->student)){
                if($value['is_enabled']===0)continue;
                $var=$this->getStudentMarks($request->user()->id,$value['id']);
                if(!is_null($var)){
                    $temp+=$var;
                    $temp+=array('completed'=>true);
                }
                else $temp+=array('completed'=>false);
            }
            array_push($arr, $temp);
        }
        return response()->json($arr);
    }

    public function getExamQuestions($id)
    {
        $exam = Exam::find($id);
        return response()->json($exam->questions->map->only(['body', 'id']));
    }

    public function sendQ1(Request $request, $id)
    {
        $data = $request->validate([
            'question' => 'required',
            'answers' => 'required',
            'AnswersConf' => 'required',
        ]);

        $question = new Question();
        $question->body = $data['question'];
        $question->time = $data['AnswersConf']['time'];
        $question->exam_id = $id;
        $question->type = 'mc';

        $question->save();

        foreach ($data['answers'] as $key => $value) {
            if (!empty($value)) {
                $ans = new Answer();
                $ans->body = $value;
                $ans->is_correct = $data['AnswersConf'][$key . 'c'];
                $ans->question_id = $question->id;
                $ans->save();
            }
        }
        return response(['status' => 'OK']);
    }

    public function sendQ23(Request $request, $id)
    {
        $data = $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'time' => 'required',
            'type' => 'required',
        ]);

        $q = new Question();
        $q->body = $data['question'];
        $q->time = $data['time'];
        $q->exam_id = $id;
        $q->type = $data['type'];

        $q->save();

        $ans = new Answer();
        $ans->body = $data['answer'];
        $ans->is_correct = true;
        $ans->question_id = $q->id;

        $ans->save();

        return response(['status' => 'OK']);
    }

    public function getQuestion($id)
    {
        $arr = [];
        $q = Question::find($id);
        $ans = $q->answers->map->only(['body', 'id', 'is_correct']);
        $arr['question'] = $q->body;
        $arr['answer'] = $ans;
        $arr['time'] = $q->time;
        $arr['id'] = $q->id;
        return response()->json($arr);
    }

    public function updateQ1(Request $request, $id)
    {
        $data = $request->validate([
            'question' => 'required',
            'answers' => 'required',
            'AnswersConf' => 'required',
            'ids' => 'required',
        ]);

        Question::find($id)->update([
            'body' => $data['question'],
            'time' => $data['AnswersConf']['time'],
        ]);

        $q = Question::find($id);
        foreach ($data['ids'] as $k => $v) {
            Answer::find($v)->update([
                'body' => $data['answers'][$k],
                'is_correct' => $data['AnswersConf'][$k],
            ]);
        }
        return response()->json(['status' => 'OK']);
    }

    public function updateQ23(Request $request, $id)
    {
        $data = $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'time' => 'required',
            'id' => 'required',
        ]);

        Question::find($id)->update([
            'body' => $data['question'],
            'time' => $data['time'],
        ]);

        $q = Question::find($id);
        Answer::find($data['id'])->update([
            'body' => $data['answer'],
        ]);

        return response()->json(['status' => 'OK']);
    }



    public function initExam($id)
    {
        $questions = Exam::find($id)->questions->shuffle();

        $arr = [];

        foreach($questions as $v){
            if($v->type==='mc'){
                array_push($arr,[
                    'question'=>$v->only(['id','body','time','type']),
                    'answer'=>$v->answers->map->only(['id','body']),
                ]);
            }else array_push($arr,['question'=>$v->only(['id','body','time','type'])]);
        }

        return response()->json($arr);
    }

    public function nextQuestion(Request $request,$id)
    {
        $ans=$request->answers;

        foreach($ans as $v){
            $sa=new StudentAnswer();
            $sa->body=$v['answer'];
            $sa->question_id=$v['qid'];
            $sa->exam_id=$id;
            $sa->student_id=$request->user()->student->id;
            $sa->save();
        }

        Exam::find($id)->students()->attach($request->user()->student->id,[
            'mark'=>$this->getStudentMarks($request->user()->student->id,$id)['mark'],
            'feedback'=>'ok',
        ]);
        

        return ['status'=>'OK'];
    }

    public function getRandomRooms(Request $request)
    {
        $rooms=Room::all()->take(10)->map->only(['name','id','type'])->shuffle();
        $sturooms=$request->user()->student->rooms->map->only(['name','id','type']);
        return response()->json($this->compare($sturooms,$rooms));

    }

    public function handStudentTask(Request $request,$id)
    {
        $request->validate(['stuFile'=>'required']);
        if($request->stuFile->isValid()){
            $path=Storage::putFile('/uploads',$request->stuFile);

            $file=new File();

            $file->name = $request->stuFile->getClientOriginalName();
            $file->ext = $request->stuFile->getClientOriginalExtension();
            $file->path = base_path() . '/storage/app/' . $path;
            $file->user_id = $request->user()->id;
            $file->size = $request->stuFile->getSize();
            $file->save();

            Assignment::find($id)->students()->attach($request->user()->id,[
                'handed_file'=>$file->id,
                'haded_date'=>Carbon::now(),
            ]);

            return 'added';
        }

        return response('invalid file');
    }

    function updateTaskVals(Request $request,$id)
    {
        $data=$request->validate([
            'marks'=>'required',
            'feedback'=>'required',
        ]);
        foreach($data['marks'][0] as $k=>$v){
            Assignment::find($id)->students()->updateExistingPivot(intval($k),['mark'=>$v]);
        }
        foreach($data['feedback'][0] as $k=>$v){
            Assignment::find($id)->students()->updateExistingPivot(intval($k),['feedback'=>$v]);
        }

        return 'updated';
    }

    public function getExamMarks($id)
    {
        $stu=Exam::find($id)->students;//->map->only(['student_id','mark']);
        $arr=[];
        foreach($stu as $v){
            $temp=array('name'=>Student::find(intval($v['pivot']->student_id))->user->name);
            $temp+=array('mark'=>$v['pivot']->mark);
            array_push($arr,$temp);
        }

        return response()->json($arr);
    }
}
