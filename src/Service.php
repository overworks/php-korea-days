<?php

namespace Minhyung\KoreaDays;

use GuzzleHttp\Client;

class Service
{
    private ?Client $client = null;

    public function __construct(
        private string $serviceKey
    ) {
        //
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

    protected function client(): Client
    {
        if (is_null($this->client)) {
            $this->client = new Client([
                'base_uri' => 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/',
            ]);
        }
        return $this->client;
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
            '_type' => 'xml',
            'solYear' => $year,
            'pageNo' => $pageNo,
            'numOfRows' => $numOfRows,
        ];
        if ($month) {
            $params['solMonth'] = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        }

        $response = $this->client()->get($method, ['query' => $params]);

        $responseData = simplexml_load_string((string) $response->getBody());

        $header = $responseData->header;
        $resultCode = (string) $header->resultCode;
        if ($resultCode !== '00') {
            throw new ApiException((string) $header->resultMsg, (int) $resultCode);
        }
        
        $body = $responseData->body;
        $items = [];
        foreach ($body->items->item as $item) {
            $items[] = json_decode(json_encode($item), true);
        }

        return [
            'items' => $items,
            'numOfRows' => (int) $body->numOfRows,
            'pageNo' => (int) $body->pageNo,
            'totalCount' => (int) $body->totalCount,
        ];
    }
}
