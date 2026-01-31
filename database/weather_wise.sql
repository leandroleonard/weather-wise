create database weather_wise;

use weather_wise;

-- user table
create table
    users (
        id varchar(32) primary key,
        name varchar(255) not null,
        email varchar(50) not null unique,
        pass text not null,
        profile_picture varchar(255) null,
        twofa_enabled tinyint (1) default 0,
        twofa_secret text null,
        city_id int null,
        has_completed_setup tinyint(1) default 0,
        last_login datetime null,
        email_verified_at datetime null,
        created_at datetime default current_timestamp,
        updated_at datetime null,
        deleted_at datetime null
    );

DELIMITER ;;

CREATE TRIGGER `trg_insert_users` BEFORE INSERT ON `users` FOR EACH ROW
BEGIN
	IF (NEW.`id` IS NULL OR NEW.`id` = '')
	THEN
		SET NEW.`id` = REPLACE(UUID(), "-", "");

	END IF;
END;;

DELIMITER ;

create table
    user_settings (
        id int primary key auto_increment,
        user_id varchar(32) not null,
        property varchar(50) not null,
        `value` text null,
        created_at datetime default current_timestamp,
        updated_at datetime null,
        key fk_user_settings_user_id (user_id),
        constraint fk_user_settings_user_id foreign key (user_id) references users (id) on delete cascade
    );

create table
    settings (
        id int primary key auto_increment,
        api varchar(20) null,
        property varchar(50) not null,
        `value` text null,
        created_at datetime default current_timestamp,
        updated_at datetime null
    );

create table
    cities (
        id int primary key auto_increment,
        name varchar(100) not null,
        slug varchar(100) not null unique,
        code varchar(20) null,
        country varchar(2) not null,
        state varchar(100) null,
        lat decimal(10, 7) not null,
        lon decimal(10, 7) not null,
        timezone varchar(50) null,
        timezone_offset int null,
        is_featured tinyint (1) default 0,
        created_at datetime default current_timestamp,
        updated_at datetime null,
        index idx_slug (slug),
        index idx_featured (is_featured),
        index idx_lat_lon (lat, lon)
    );

-- Current weater (cache)
create table
    weather (
        id int primary key auto_increment,
        city_id int not null,
        dt datetime not null,
        sunrise datetime null,
        sunset datetime null,
        temperature decimal(5, 2) null comment 'Temperature in Celsius',
        feels_like decimal(5, 2) null,
        temp_min decimal(5, 2) null,
        temp_max decimal(5, 2) null,
        pressure int null comment 'Pressure in hPa',
        humidity int null comment 'Humidity in %',
        dew_point decimal(5, 2) null,
        clouds int null,
        uvi decimal(4, 2) null,
        visibility int null,
        wind_speed decimal(5, 2) null,
        wind_gust decimal(5, 2) null,
        wind_deg int null,
        rain_1h decimal(5, 2) null,
        snow_1h decimal(5, 2) null,
        pop decimal(3, 2) null,
        weather_id int null,
        weather_main varchar(50) null,
        weather_description varchar(255) null,
        weather_icon varchar(10) null,
        cached_at datetime default current_timestamp,
        expires_at datetime null,
        created_at datetime default current_timestamp,
        updated_at datetime null,
        key fk_weather_city_id (city_id),
        constraint fk_weather_city_id foreign key (city_id) references cities (id) on delete cascade,
        index idx_city_cached (city_id, cached_at),
        index idx_expires (expires_at)
    );

create table
    weather_daily (
        id int primary key auto_increment,
        city_id int not null,
        forecast_date date not null,
        dt datetime not null,
        sunrise datetime null,
        sunset datetime null,
        moonrise datetime null,
        moonset datetime null,
        moon_phase decimal(3, 2) null,
        summary text null,
        temp_day decimal(5, 2) null,
        temp_night decimal(5, 2) null,
        temp_eve decimal(5, 2) null,
        temp_morn decimal(5, 2) null,
        temp_min decimal(5, 2) null,
        temp_max decimal(5, 2) null,
        feels_like_day decimal(5, 2) null,
        feels_like_night decimal(5, 2) null,
        feels_like_eve decimal(5, 2) null,
        feels_like_morn decimal(5, 2) null,
        pressure int null,
        humidity int null,
        dew_point decimal(5, 2) null,
        wind_speed decimal(5, 2) null,
        wind_gust decimal(5, 2) null,
        wind_deg int null,
        clouds int null,
        uvi decimal(4, 2) null,
        pop decimal(3, 2) null,
        rain decimal(5, 2) null,
        snow decimal(5, 2) null,
        weather_id int null,
        weather_main varchar(50) null,
        weather_description varchar(255) null,
        weather_icon varchar(10) null,
        cached_at datetime default current_timestamp,
        expires_at datetime null,
        created_at datetime default current_timestamp,
        updated_at datetime null,
        key fk_daily_city_id (city_id),
        constraint fk_daily_city_id foreign key (city_id) references cities (id) on delete cascade,
        unique key uk_city_date (city_id, forecast_date),
        index idx_forecast_date (forecast_date),
        index idx_expires_daily (expires_at)
    );

create table
    weather_hourly (
        id int primary key auto_increment,
        city_id int not null,
        dt datetime not null,
        temperature decimal(5, 2) null,
        feels_like decimal(5, 2) null,
        pressure int null,
        humidity int null,
        dew_point decimal(5, 2) null,
        clouds int null,
        uvi decimal(4, 2) null,
        visibility int null,
        wind_speed decimal(5, 2) null,
        wind_gust decimal(5, 2) null,
        wind_deg int null,
        pop decimal(3, 2) null,
        rain_1h decimal(5, 2) null,
        snow_1h decimal(5, 2) null,
        weather_id int null,
        weather_main varchar(50) null,
        weather_description varchar(255) null,
        weather_icon varchar(10) null,
        cached_at datetime default current_timestamp,
        expires_at datetime null,
        created_at datetime default current_timestamp,
        updated_at datetime null,
        key fk_hourly_city_id (city_id),
        constraint fk_hourly_city_id foreign key (city_id) references cities (id) on delete cascade,
        unique key uk_city_hour (city_id, dt),
        index idx_dt (dt),
        index idx_expires_hourly (expires_at)
    );

create table
    weather_alerts (
        id int primary key auto_increment,
        city_id int not null,
        sender_name varchar(255) null,
        event varchar(255) not null,
        start_time datetime not null,
        end_time datetime not null,
        description text null,
        tags json null,
        cached_at datetime default current_timestamp,
        expires_at datetime null,
        created_at datetime default current_timestamp,
        updated_at datetime null,
        key fk_alerts_city_id (city_id),
        constraint fk_alerts_city_id foreign key (city_id) references cities (id) on delete cascade,
        index idx_active_alerts (city_id, start_time, end_time),
        index idx_expires_alerts (expires_at)
    );

alter table users add key fk_users_city_id (city_id),
add constraint fk_users_city_id foreign key (city_id) references cities (id) on delete set null;

insert into
    cities (
        name,
        slug,
        country,
        state,
        lat,
        lon,
        timezone,
        timezone_offset,
        is_featured
    )
values
    (
        'São Paulo',
        'sao-paulo',
        'BR',
        'São Paulo',
        -23.5505199,
        -46.6333094,
        'America/Sao_Paulo',
        -10800,
        1
    ),
    (
        'Rio de Janeiro',
        'rio-de-janeiro',
        'BR',
        'Rio de Janeiro',
        -22.9068467,
        -43.1728965,
        'America/Sao_Paulo',
        -10800,
        1
    ),
    (
        'Londres',
        'london',
        'GB',
        'England',
        51.5073509,
        -0.1277583,
        'Europe/London',
        0,
        1
    ),
    (
        'Nova York',
        'new-york',
        'US',
        'New York',
        40.7127753,
        -74.0059728,
        'America/New_York',
        -18000,
        1
    ),
    (
        'Tóquio',
        'tokyo',
        'JP',
        'Tokyo',
        35.6761919,
        139.6503106,
        'Asia/Tokyo',
        32400,
        1
    );

insert into
    settings (api, property, `value`)
values
    ('openweather', 'api_key', 'YOUR_API_KEY_HERE'),
    ('openweather', 'cache_duration_current', '1800'),
    ('openweather', 'cache_duration_daily', '21600'),
    ('openweather', 'cache_duration_hourly', '3600');
