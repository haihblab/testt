<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CheckAuthenticationException;
use App\Exceptions\CheckAuthorizationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\QueryException;
use App\Exceptions\ServerException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Controllers\AbstractController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends AbstractController
{
    protected $guard = 'api';

    /**
     * get the data for the GET api method
     *
     * @param callable $callback
     * @param $code
     * @return JsonResponse
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws CheckAuthorizationException
     * @throws CheckAuthenticationException
     * @throws UnprocessableEntityException
     */
    protected function getData(callable $callback, $code = Response::HTTP_OK)
    {
        try {
            $data = call_user_func_array($callback, []);

            return $this->renderJson($data, $code);
        } catch (UnprocessableEntityException $e) {
            Log::info($e->getMessage());
            throw new UnprocessableEntityException($e->getMessage());
        } catch (QueryException $e) {
            Log::info($e->getMessage());
            throw new QueryException($e->getMessage());
        } catch (NotFoundException $e) {
            Log::info($e->getMessage());
            throw new NotFoundException($e->getMessage());
        } catch (CheckAuthorizationException $e) {
            Log::info($e->getMessage());
            throw new CheckAuthorizationException($e->getMessage());
        } catch (CheckAuthenticationException $e) {
            Log::info($e->getMessage());
            throw new CheckAuthenticationException($e->getMessage());
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ServerException();
        }
    }

    /**
     * Process the request to the server with the external method Get
     *
     * @param callable $callback
     * @param $code
     * @return JsonResponse
     * @throws NotFoundException
     * @throws QueryException
     * @throws ServerException
     * @throws CheckAuthorizationException
     * @throws UnprocessableEntityException
     */
    protected function doRequest(callable $callback, $code = Response::HTTP_OK)
    {
        DB::beginTransaction();
        try {
            $results = call_user_func_array($callback, []);
            DB::commit();

            return $this->renderJson($results, $code);
        } catch (UnprocessableEntityException $e) {
            Log::info($e->getMessage());
            throw new UnprocessableEntityException($e->getMessage());
        } catch (QueryException $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            throw new QueryException($e->getMessage());
        } catch (NotFoundException $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            throw new NotFoundException($e->getMessage());
        } catch (CheckAuthorizationException $e) {
            Log::info($e->getMessage());
            throw new CheckAuthorizationException($e->getMessage());
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            throw new ServerException();
        }
    }

    /**
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    public function renderJson($data = [], $code = Response::HTTP_OK)
    {
        return response()->json($data, $code);
    }
}
