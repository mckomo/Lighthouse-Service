<?php namespace Lighthouse\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Lighthouse\Http\Controllers\Controller;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Common\ResultCodes;
use Lighthouse\Services\Torrents\Contracts\Service;
use Lighthouse\Services\Torrents\Entities\Query;
use Lighthouse\Services\Torrents\Entities\Torrent;
use GuzzleHttp\Client as HttpClient;
use Symfony\Component\HttpFoundation\Response;

class Torrents extends Controller {

    protected $service;

    function __construct(Service $service, HttpClient $http)
    {
        $this->service = $service;
        $this->http = $http;
    }

    /**
     * @param Request $request
     * @return Response
     */
	public function search(Request $request)
	{
        $query = $this->buildQuery($request->input());
        $operationResult = $this->service->search($query);

        return $this->prepareResponse($operationResult);
	}

    /**
     * @param string $hash
     * @return Response
     */
    public function getEntity($hash)
    {
        $operationResult = $this->service->get($hash);

        return $this->prepareResponse($operationResult);
    }

    /**
     * @param string $hash
     * @return Response
     */
    public function getFile($hash)
    {
        $operationResult = $this->service->get($hash);

        if ($operationResult->isFailed())
            $this->prepareResponse($operationResult);

        $torrent = $operationResult->getData();

        $filename = Str::slug($torrent->name) . '.torrent';
        $contents = $this->download($torrent);

        if (is_null($contents))
            return response('', 404);

        return response($contents)
            ->header('Content-Type', 'application/x-bittorrent')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function prepareResponse(OperationResult $result)
    {
        $code = $this->mapToHttpCode($result->getCode());
        $body = [];

        if ($result->isSuccessful())
        {
            $data = $result->getData();
            $body = $this->mapTorrents($data);
        }
        else
        {
            $body['error'] = $result->getError();
        }

        return response($body, $code);
    }

    /**
     * @param array|Torrent $torrents
     * @return array|Torrent
     */
    private function mapTorrents($torrents)
    {
        if (is_array($torrents))
            return array_map([$this, 'prepareTorrent'], $torrents);

        return $this->prepareTorrent($torrents);
    }

    /**
     * @param Torrent $torrent
     * @return array
     */
    private function prepareTorrent($torrent)
    {
        return $this
            ->replaceUrl($torrent)
            ->toArray();
    }

    /**
     * @param Torrent $torrent
     * @return Torrent
     */
    private function replaceUrl(Torrent $torrent)
    {
        $fileUrl = route('torrent/file', ['hash' => $torrent->hash]);
        $torrent->url = $fileUrl;

        return $torrent;
    }

    /**
     * @param int $resultCode
     * @return int
     */
    private function mapToHttpCode($resultCode)
    {
        switch($resultCode)
        {
            case ResultCodes::Successful:
                return 200;
            case ResultCodes::ResourceCreated;
                return 201;
            case ResultCodes::InvalidInput:
                return 400;
            case ResultCodes::ResourceNotFound:
                return 404;
            case ResultCodes::ServiceError:
                return 500;
            default:
                throw new \InvalidArgumentException('Service returned unsupported result code.');
        }
    }

    /**
     * @param array $input
     * @return Query
     */
    private function buildQuery($input)
    {
        $params = [
            'phrase' => array_key_exists('q', $input) ? $input['q'] : ''
        ];

        if (array_key_exists('size', $input))
            $params['size'] = $input['size'];

        if (array_key_exists('category', $input))
            $params['category'] = strtolower($input['category']);

        return new Query($params);
    }

    /**
     * @param Torrent $torrent
     * @return string
     */
    private function download(Torrent $torrent)
    {
        $response = $this->http->get($torrent->url, [
            'headers' => ['Accept-Encoding' => 'gzip'],
            'decode_content' => true
        ]);

        return $response->getStatusCode() == 200
            ? $response->getBody()
            : null;

    }

}
