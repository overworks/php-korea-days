# PHP Korea Days

한국천문연구원에서 제공하는 특일 정보 API의 PHP 래퍼 라이브러리입니다.

## 소개

이 라이브러리는 한국천문연구원의 특일 정보 API를 쉽게 사용할 수 있도록 PHP로 래핑한 것입니다.
국경일, 공휴일, 기념일, 24절기, 잡절 등 한국의 다양한 특별한 날짜 정보를 조회할 수 있습니다.

### 주요 기능

- 🎉 **국경일 정보 조회** - 3.1절, 광복절, 개천절 등
- 📅 **공휴일 정보 조회** - 설날, 추석, 어린이날 등
- 🎊 **기념일 정보 조회** - 식목일, 과학의 날, 스승의 날 등
- 🌸 **24절기 정보 조회** - 입춘, 춘분, 하지, 동지 등
- 📆 **잡절 정보 조회** - 한식, 초복, 중복, 말복 등

## 요구사항

- PHP 8.0 이상
  - 현재 Composer 설정에는 PHP 버전 제약이 없지만, 이 라이브러리는 PHP 8.0 이상 환경에서만 동작합니다.
- PSR-18 HTTP 클라이언트 구현체 (예: Guzzle)
- PSR-17 HTTP Factory 구현체 (예: guzzlehttp/psr7, nyholm/psr7)
- 공공데이터포털에서 발급받은 API 서비스 키

## 설치

Composer를 사용하여 설치할 수 있습니다:

```bash
composer require minhyung/korea-days
```

PSR-18 HTTP 클라이언트와 PSR-17 HTTP Factory가 없다면 Guzzle을 함께 설치하세요:

```bash
composer require minhyung/korea-days guzzlehttp/guzzle guzzlehttp/psr7
```

## API 키 발급

1. [공공데이터포털](https://www.data.go.kr/data/15012690/openapi.do)에 접속
2. 회원가입 및 로그인
3. "한국천문연구원_특일 정보" API 신청
4. 발급받은 서비스 키를 사용

## 사용법

### 기본 사용법

```php
use Minhyung\KoreaDays\Service;

// 서비스 객체 생성
$service = new Service('YOUR_SERVICE_KEY');

// 2024년 9월의 국경일 조회
$result = $service->getHoliDeInfo(2024, 9);

// 결과 출력
print_r($result);
```

### 응답 구조

모든 API 메서드는 동일한 구조의 배열을 반환합니다:

```php
[
    'items' => [
        // 조회된 특일 정보 배열
        [
            'dateKind' => '01',           // 날짜 구분 코드
            'dateName' => '삼일절',       // 날짜 이름
            'isHoliday' => 'Y',           // 공휴일 여부
            'locdate' => '20240301',      // 날짜
            'seq' => '1',                 // 순번
        ],
        // ...
    ],
    'numOfRows' => 10,      // 한 페이지 결과 수
    'pageNo' => 1,          // 페이지 번호
    'totalCount' => 3,      // 전체 결과 수
]
```

### 사용 가능한 메서드

#### 1. 국경일 정보 조회

```php
$result = $service->getHoliDeInfo(
    year: 2024,        // 연도 (필수)
    month: 3,          // 월 (선택, 생략시 연간 전체)
    pageNo: 1,         // 페이지 번호 (기본값: 1)
    numOfRows: 10      // 페이지당 결과 수 (기본값: 10)
);
```

#### 2. 공휴일 정보 조회

```php
$result = $service->getRestDeInfo(
    year: 2024,
    month: 9,
    pageNo: 1,
    numOfRows: 10
);
```

#### 3. 기념일 정보 조회

```php
$result = $service->getAnniversaryInfo(
    year: 2024,
    month: 5,
    pageNo: 1,
    numOfRows: 20      // 기념일은 많을 수 있으므로 필요시 증가
);
```

#### 4. 24절기 정보 조회

```php
// 연간 24절기 전체 조회 (월 생략)
$result = $service->get24DivisionsInfo(
    year: 2024,
    month: null,       // null로 설정하면 연간 전체
    pageNo: 1,
    numOfRows: 24      // 24절기를 모두 가져오려면 24로 설정
);
```

#### 5. 잡절 정보 조회

```php
$result = $service->getSundryDayInfo(
    year: 2024,
    month: 7,
    pageNo: 1,
    numOfRows: 10
);
```

### 사용자 정의 HTTP 클라이언트

PSR-18 호환 HTTP 클라이언트를 직접 제공할 수 있습니다:

```php
use GuzzleHttp\Client;
use Minhyung\KoreaDays\Service;

$httpClient = new Client([
    'timeout' => 10,
    'verify' => true,
]);

$service = new Service('YOUR_SERVICE_KEY', $httpClient);
```

## 예외 처리

API 호출 중 오류가 발생하면 `ApiException`이 발생합니다:

```php
use Minhyung\KoreaDays\Service;
use Minhyung\KoreaDays\ApiException;

try {
    $service = new Service('YOUR_SERVICE_KEY');
    $result = $service->getHoliDeInfo(2024, 3);
} catch (ApiException $e) {
    echo "API 오류: " . $e->getMessage();
    echo "오류 코드: " . $e->getCode();
    echo "HTTP 상태 코드: " . $e->getStatusCode();
}
```

## 테스트

테스트를 실행하려면 환경 변수에 서비스 키를 설정해야 합니다:

```bash
# .env 파일 또는 환경 변수로 설정
export SERVICE_KEY=your_service_key_here

# 테스트 실행
composer test
```

## 라이선스

이 프로젝트는 MIT 라이선스 하에 배포됩니다. 자세한 내용은 [LICENSE](LICENSE) 파일을 참조하세요.

## 관련 링크

- [공공데이터포털 - 한국천문연구원 특일 정보](https://www.data.go.kr/data/15012690/openapi.do)
- [한국천문연구원 정보공개&활용](https://astro.kasi.re.kr/information/pageView/31)
- [GitHub 저장소](https://github.com/overworks/php-korea-days)
- [이슈 트래커](https://github.com/overworks/php-korea-days/issues)

## 기여

버그 리포트, 기능 제안, 풀 리퀘스트는 언제나 환영합니다!

## 작성자

- **Minhyung Park** - [urlinee@gmail.com](mailto:urlinee@gmail.com)
