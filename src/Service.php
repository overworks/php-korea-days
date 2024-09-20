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
     * @return array
     */
    public function getHolidays($year, $month = null, int $pageNo = 1, int $numOfRows = 10): array
    {
        $params = [
            'serviceKey' => $this->serviceKey,
            '_type' => 'json',
            'solYear' => $year,
            'pageNo' => $pageNo,
            'numOfRows' => $numOfRows,
        ];
        if ($month) {
            $params['solMonth'] = str_pad($month, 2, '0', STR_PAD_LEFT);
        }

        $response = $this->client()->get('getHoliDeInfo', ['query' => $params]);

        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['response']['body'];
    }

    /**
     * 공휴일 정보 조회
     *
     * @param  int|string  $year  연
     * @param  int|string|null  $month  월
     * @param  int  $pageNo  페이지 번호
     * @param  int  $numOfRows  한 페이지 결과 수
     * @return array
     */
    public function getRestDays($year, $month = null, int $pageNo = 1, int $numOfRows = 10): array
    {
        $params = [
            'serviceKey' => $this->serviceKey,
            '_type' => 'json',
            'solYear' => $year,
            'pageNo' => $pageNo,
            'numOfRows' => $numOfRows,
        ];
        if ($month) {
            $params['solMonth'] = str_pad($month, 2, '0', STR_PAD_LEFT);
        }

        $response = $this->client()->get('getRestDeInfo', ['query' => $params]);

        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['response']['body'];
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
}
