<?php

namespace App\Services;

use App\Model\VNSQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VNSQuestionService
{
    /**
     * Get all active questions
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllQuestions()
    {
        return VNSQuestion::where('del_flag', "N")
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get question by ID
     *
     * @param int $id
     * @return VNSQuestion
     */
    public function getQuestionById($id)
    {
        return VNSQuestion::where('id', $id)
            ->where('del_flag', "N")
            ->firstOrFail();
    }

    /**
     * Create a new question
     *
     * @param array $data
     * @return VNSQuestion
     */
    public function createQuestion(array $data)
    {
        try {
            DB::beginTransaction();

            $question = VNSQuestion::create([
                'question_name' => $data['question_name'],
                'template_type' => $data['template_type'] ?? null,
                
                'created_date' => now(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return $question;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing question
     *
     * @param int $id
     * @param array $data
     * @return VNSQuestion
     */
    public function updateQuestion($id, array $data)
    {
        try {
            DB::beginTransaction();

            $question = $this->getQuestionById($id);

            $question->update([
                'question_name' => $data['question_name'],
                'template_type' => $data['template_type'] ?? null,
                'updated_date' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return $question;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Soft delete a question
     *
     * @param int $id
     * @return bool
     */
    public function deleteQuestion($id)
    {
        try {
            DB::beginTransaction();

            $question = $this->getQuestionById($id);

            $question->update([
                'del_flag' => "Y",
                'deleted_date' => now(),
                'deleted_by' => Auth::id(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get questions list with pagination for AJAX
     *
     * @param array $search
     * @param bool $paginate
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getList($search, $paginate = true)
    {
        $query = VNSQuestion::select('vns_question.*', 'users.first_name', 'users.last_name', 'template_master.template_name')
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'vns_question.created_by');
            })
            ->leftJoin('template_master', function($join) {
                $join->on('template_master.id', '=', 'vns_question.template_type');
            })
            ->where('vns_question.del_flag', "N");

        if (isset($search['question_name']) && $search['question_name'] != "") {
            $query->where('vns_question.question_name', 'like', "%{$search['question_name']}%");
        }

        if (isset($search['template_type']) && $search['template_type'] != "") {
            $query->where('vns_question.template_type', 'like', "%{$search['template_type']}%");
        }

        $query->orderBy('vns_question.id', 'desc');

        return $paginate ? $query->paginate(50) : $query->get();
    }

    /**
     * Check if question name already exists
     *
     * @param string $questionName
     * @param int|null $excludeId
     * @return bool
     */
    public function isQuestionNameExists($questionName, $excludeId = null)
    {
        $query = VNSQuestion::where('question_name', $questionName)
            ->where('del_flag', "N");

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get questions by template type
     *
     * @param string $templateType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getQuestionsByTemplateType($templateType)
    {
        return VNSQuestion::where('template_type', $templateType)
            ->where('del_flag',  "N")
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Get question count
     *
     * @return int
     */
    public function getQuestionCount()
    {
        return VNSQuestion::where('del_flag',  "N")->count();
    }
}
