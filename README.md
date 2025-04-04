# Meeting Room Booking System (Vue, Laravel and Docker)

## The design I put together

In order to be able to lock rows in the database as a mechanism to deal with the concurrency of the petitions, I created a table of hourly gaps (depending on each room and each day). With more time, I can create a more flexible system that allows to go down to the minute. So when a user wants to reserve a room for a date between certain hours, the API first checks whether those hour gaps are reserved or not, and if not, then tries to lock all the database rows representing those hours. If it's possible to get the lock, then the reservation data is set within a transaction. (With more time, I would like to include a mechanism to remove past data from this table to prevent it from growing too much).

I took a creative licence and added a new endpoint to the specification. The GET /available-rooms?date=YYYY-MM-DD&time=HH:mm endpoint is useful for checking if a particular hour is taken for a date and room, but because the system allows you to reserve a group of hours, I would have to call the endpoint for each hour to check for availability. So the new endpoint is similar, but takes two parameters for start_time and end_time and checks all the desired hours at once. I've kept the previous one to fulfill the original specification.

The only seed data created in the database is for the rooms. I have included 5 rooms with simple names to test the system.

## Installation

It's inside a Docker Composer structure. So please, install Docker and Docker Composer and navigate the project folder to execute:

```sh
docker compose up --build --abort-on-container-exit --remove-orphans
```

An then navigate with the browser to

```sh
http://127.0.0.1:8080/
```

## To launch the test on Laravel, once the docker compose system is running:

Identify the backend container ID with

```sh
docker ps
```

In my local is d0f1d202afef, so then call to connect to the shell of the Docker Laravel

```sh
docker exec -it d0f1d202afef sh
```

Once connected, run the tests with

```sh
php artisan test --testsuite=Booking
```

## Comments for improvements or next steps

- I honestly hate using magic numbers, but to be quick I've used hourly limits (9:00 - 18:00) as hardcoded values. So sorry, need to be defined dynamically.
- A visual calendar with hours coloured according to current occupancy. Or a system to check which rooms are available for a particular date.
- Send a Google Cal appointment to all guests.
- Share the new meeting on Slack or similar, mentioning the guests.
- Options to move, edit or remove the appointment (and feedback).
- Add additional info of each room: number of seats, clothes, floor, etc... so you can filter according to the needs of your meeting.
- If these are virtual rooms, we need to add time zones to support people from different time zones.
- Filter and search options in the booking table. Also some kind of format to easily identify each room and each user booking.
- Probably a better visual system for the steps to take to reserve a room.

## Going the extra mile 🚀

I created some feature tests on the Laravel side to check that all the API endpoints are working properly. With more time, I could also cover unit tests and component tests for the Vue side.

When I do these kinds of test projects, I like to spend some of the time putting together a Docker Compose stack that works well, so in the future I have a base to test different projects or ideas with a boilerplate. At first I tried to build a production-ready system, but after spending too much time on it, I decided to accept a development version. Sorry for being weak.
