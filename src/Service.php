<?php

namespace Minhyung\KoreaDays;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class Service
{
    const BASE_URL = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/';

    private ?ClientInterface $client = null;

    /**
     * 생성자
     * 
     * @param  string  $serviceKey  공공데이터포털에서 발급받은 서비스 키
     * @param  \Psr\Http\Client\ClientInterface|null  $client  PSR-18 htt-client 인터페이스
     * @return void
     */
    public function __construct(
        private string $serviceKey,
        ?ClientInterface $client = null
    ) {
        $this->client = $client ??= new GuzzleAdapter();
    }

    /**
     * 국경일 정보 조회
     *
     * @param  int|string  $year  연
     * @param  int|string|null  $month  월
     * @param  int  $pageNo  페이지 번호
     * @param  int  $numOfRows  한 페이지 결과 수
     * @return array<string, mixed>
     */
    public function getHoliDeInfo($year, $month = null, int $pageNo = 1, int $numOfRows = 10): array
    {
        return $this->request('getHoliDeInfo', $year, $month, $pageNo, $numOfRows);
    }

    /**
     * 공휴일 정보 조회
     *
     * @param  int|string  $year  연
     * @param  int|string|null  $month  월
     * @param  int  $pageNo  페이지 번호
     * @param  int  $numOfRows  한 페이지 결과 수
     * @return array<string, mixed>
     */
    public function getRestDeInfo($year, $month = null, int $pageNo = 1, int $numOfRows = 10): array
    {
        return $this->request('getRestDeInfo', $year, $month, $pageNo, $numOfRows);
    }

    /**
     * 기념일 정보 조회
     * 
     * @param  int|string  $year  연
     * @param  int|string|null  $month  월
     * @param  int  $pageNo  페이지 번호
     * @param  int  $numOfRows  한 페이지 결과 수
     * @return array<string, mixed>
     */
    public function getAnniversaryInfo($year, $month = null, int $pageNo = 1, int $numOfRows = 10): array
    {
        return $this->request('getAnniversaryInfo', $year, $month, $pageNo, $numOfRows);
    }

    /**
     * 24절기 정보 조회
     * 
     * @param  int|string  $year  연
     * @param  int|string|null  $month  월
     * @param  int  $pageNo  페이지 번호
     * @param  int  $numOfRows  한 페이지 결과 수
     * @return array<string, mixed>
     */
    public function get24DivisionsInfo($year, $month = null, int $pageNo = 1, int $numOfRows = 10): array
    {
        return $this->request('get24DivisionsInfo', $year, $month, $pageNo, $numOfRows);
    }

    /**
     * 잡절 정보 조회
     * 
     * @param  int|string  $year  연
     * @param  int|string|null  $month  월
     * @param  int  $pageNo  페이지 번호
     * @param  int  $numOfRows  한 페이지 결과 수
     * @return array<string, mixed>
     */
    public function getSundryDayInfo($year, $month = null, int $pageNo = 1, int $numOfRows = 10): array
    {
        return $this->request('getSundryDayInfo', $year, $month, $pageNo, $numOfRows);
    }

    /**
     * 실제 API 호출부
     * 
     * @param  string  $method  API 메소드
     * @param  int|string  $year  연
     * @param  int|string|null  $month  월
     * @param  int  $pageNo  페이지 번호
     * @param  int  $numOfRows  한 페이지 결과 수
     * @return array<string, mixed>
     */
    protected function request($method, $year, $month = null, int $pageNo = 1, int $numOfRows = 10)
    {
        $params = [
            'serviceKey' => $this->serviceKey,
            '_type' => 'json',
            'solYear' => $year,
            'pageNo' => $pageNo,
            'numOfRows' => $numOfRows,
        ];
        if ($month) {
            $params['solMonth'] = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        }

        $uri = self::BASE_URL.$method.'?'.http_build_query($params, encoding_type: PHP_QUERY_RFC3986);
        $request = new Request('GET', $uri);

        $response = $this->client->sendRequest($request);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            $body = '';
            $contentType = $response->getHeaderLine('Content-Type');
            if (stripos($contentType, 'text/plain') !== false) {
                $body = (string) $response->getBody();
            }
            throw new ApiException($body ?: 'API 요청 실패', 0, $statusCode);
        }

        $responseData = json_decode((string) $response->getBody(), true);

        $header = $responseData['response']['header'] ?? [];
        $resultCode = $header['resultCode'] ?? null;
        if ($resultCode !== '00') {
            throw new ApiException($header['resultMsg'], (int) $resultCode, $statusCode);
        }
        
        $body = $responseData['response']['body'] ?? [];
        return [
            'items' => $body['items']['item'] ?? [],
            'numOfRows' => (int) $body['numOfRows'],
            'pageNo' => (int) $body['pageNo'],
            'totalCount' => (int) $body['totalCount'],
        ];
    }
}
