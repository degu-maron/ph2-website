CREATE TABLE studies (
    id int AUTO_INCREMENT PRIMARY KEY,
    day date,
    hour int,
    language_id int,
    content_id int
);
CREATE TABLE languages (
    id int AUTO_INCREMENT PRIMARY KEY,
    language varchar(255)
);
CREATE TABLE contents (
    id int AUTO_INCREMENT PRIMARY KEY,
    content varchar(255)
);


INSERT INTO languages(language) VALUE ("HTML");
INSERT INTO languages(language) VALUE ("CSS");
INSERT INTO languages(language) VALUE ("JaavaScript");
INSERT INTO languages(language) VALUE ("PHP");
INSERT INTO languages(language) VALUE ("Laravel");
INSERT INTO languages(language) VALUE ("SQL");
INSERT INTO languages(language) VALUE ("SHELL");
INSERT INTO languages(language) VALUE ("情報システム基礎知識");

INSERT INTO contents(content) VALUE ("N予備校");
INSERT INTO contents(content) VALUE ("ドットインストール");
INSERT INTO contents(content) VALUE ("課題");

INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201001, 2.5, 1, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201002, 3.5, 2, 2);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201003, 6.0, 1, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201004, 4.5, 2, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201005, 1.0, 3, 2);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201006, 2.5, 2, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201007, 3.5, 1, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201008, 5.0, 3, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201009, 7.0, 1, 2);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201010, 2.5, 3, 2);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201011, 1.5, 2, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201012, 3.0, 4, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201013, 7.0, 3, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201014, 2.5, 4, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201015, 3.0, 2, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201016, 4.0, 1, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201017, 5.5, 4, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201018, 3.0, 3, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201019, 7.0, 5, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201020, 1.5, 6, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201021, 1.5, 2, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201022, 3.5, 7, 2);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201023, 7.5, 8, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201024, 4.5, 2, 2);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201025, 3.5, 1, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201026, 2.0, 3, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201027, 2.5, 1, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201028, 3.0, 4, 2);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201029, 6.0, 2, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201030, 4.5, 1, 1);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20201031, 2.5, 2, 3);

INSERT INTO studies(day, hour, language_id, content_id) VALUES (20230309, 6.0, 4, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20230310, 3.0, 6, 3);
INSERT INTO studies(day, hour, language_id, content_id) VALUES (20230311, 4.0, 6, 3);


-- SELECT * FROM studies INNER JOIN languages ON studies.language_id = languages.id INNER JOIN contents ON studies.content_id = contents.id;
SELECT SUM(hour) 今日の合計学習時間 FROM studies WHERE day = date(now());
SELECT SUM(hour) 今月の合計学習時間 FROM studies WHERE month(day) = month(now());
SELECT SUM(hour) 合計学習時間 FROM studies;


-- 今月末の日付を取得し、そこからの差分で今月の日付を取得する(カレンダー機能)
SELECT calendar.ymd as ymd FROM (
    SELECT DATE_FORMAT(date_add(date_add(last_day(now()), interval - day(last_day(now())) DAY) , INTERVAL td.add_day DAY), '%y-%m-%d') AS ymd FROM (
        SELECT 0 as add_day FROM dual WHERE ( @num:= 1 - 1 ) * 0 union all SELECT @num:= @num + 1 as add_day FROM `information_schema`.columns limit 31
    ) AS td
) AS calendar WHERE month(calendar.ymd) = month(now()) ORDER BY calendar.ymd;

-- カレンダー使って今月分の日毎の学習時間表示
SELECT calendar.ymd as ymd, COALESCE (studies.hour, 0) as 学習時間 FROM (
    SELECT DATE_FORMAT(date_add(date_add(last_day(now()), interval - day(last_day(now())) DAY) , INTERVAL td.add_day DAY), '%y-%m-%d') AS ymd FROM (
        SELECT 0 as add_day FROM dual WHERE ( @num:= 1 - 1 ) * 0 union all SELECT @num:= @num + 1 as add_day FROM `information_schema`.columns limit 31
    ) AS td
) AS calendar LEFT JOIN studies ON calendar.ymd = studies.day WHERE month(calendar.ymd) = month(now()) ORDER BY calendar.ymd;