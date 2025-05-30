# Database_final
# 電影與影評管理系統

本專案為一個基於 PHP + MySQL + MAMP 環境的電影評論網站，支援基本電影資訊展示、評論撰寫與使用者帳號功能，並已成功實作以下功能：

---

## 目前功能簡介

### 1. 使用者系統
- 使用者註冊 `register.php`（帳號、密碼、Email）
- 使用者登入 `login.php`
- 登出功能 `logout.php`
- 登入後顯示歡迎語與登出按鈕
- Session 管理與權限保護

### 2. 電影資訊展示
- `index.php` 顯示所有電影清單（標題、導演、上映日期、平均評分）
- `movie_detail.php` 顯示單部電影詳細資料、劇照、穿搭建議

### 3. 評論系統
- 登入後可針對每部電影撰寫影評與評分（0~5 分）
- 評論即時顯示於電影頁面下方
- 僅限發表者本人可刪除自己的評論
- 未登入者會提示需先登入才能留言

---

## 專案檔案結構（主要）
- final_project
- index.php               ->電影清單頁面
- movie_detail.php        ->單部電影詳情與評論顯示頁
- login.php               ->登入畫面
- login_process.php       ->登入處理邏輯
- logout.php              ->登出功能
- register.php            ->使用者註冊畫面
- register_process.php    ->註冊處理邏輯
- review_submit.php       ->評論送出處理
- delete_review.php       ->評論刪除功能
- config.php              ->資料庫連線設定
- images                  ->儲存電影海報、劇照等靜態圖片
---

## 未做但有想到的功能
- 評論編輯功能（目前僅支援刪除）
- 上傳圖片功能（支援海報與劇照）
- 管理員後台介面：新增電影、刪除評論、審核留言等
- 網站美觀

---

## 測試用帳號建議（可從資料庫預設值）

| 帳號 | 密碼 | 身分 |
|------|------|------|
| Alice | hashed_pw1 | User |
| Bob   | hashed_pw2 | User |
| Charlie | hashed_pw3 | Admin |

---

##  注意事項

- 要在自己電腦跑，要記得修改config.php的servername那些
- 圖片要放置於 `images/` 資料夾中，並在資料庫中修改其圖片路徑
- `session_start()` 一定要寫在每頁最開頭，否則 session 無法存取
- 資料表需搭配 `movie_db_init.sql` 與 `sample_data.sql` 建立與填充初始資料
- streamlinks的video_url 是https://www.youtube.com/embed/再加上該部影片的亂碼

