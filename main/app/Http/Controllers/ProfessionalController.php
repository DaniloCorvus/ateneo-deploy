<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessionalRequest;
use App\Models\Person;
use App\Models\Usuario;
use App\Models\WorkContract;
use App\Repositories\ProfessionalRepository;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ProfessionalController extends Controller
{
    use ApiResponser;

    protected $repository;

    public function __construct(ProfessionalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store(ProfessionalRequest $request)
    {
        try {
            return $this->success($this->repository->store());
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(), $th->getLine(), $th->getFile()], 400);
        }
    }

    public function index()
    {
        try {
            return $this->success($this->repository->paginate());
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(), $th->getLine(), $th->getFile()], 400);
        }
    }

    public function show($id)
    {
        try {
            return $this->success($this->repository->show($id));
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(), $th->getLine(), $th->getFile()], 400);
        }
    }
}
