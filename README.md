# Chatty

#### Descript
Chatty is small a real time messaging app providing seemless experience of chatting between friends and families. It includes features like messaging in private chat/ group chat, creating conversation, inviting new user to group, leave conversation, online/ offline user status, current active users in groups and even whisper typing event.


- [x] Authentication
- [x] Chatting in private and group conversation
- [x] Easily Search users
- [x] Create conversation and groups , invite or leave conversation
- [x] Online , Offline status and last seen status
- [x] Currently active users in groups
- [x] Type whispering
- [x] Profile management
- [x] Responsive
- [x] Message delete, forward and reply


### Tech Stacks:
`Laravel 11` `sqlite` `vue 3` `laravel-reverb` `tailwindcss` `shadcn-vue` `inertia` `axios` `laravel-breeeze` `laravel-echo`

### Coming soon
- [ ] Support Image, Video, and links in conversation
- [ ] Video Call & Audio Call

### Screenshots

<img src="https://github.com/Y2theK/chatty/blob/dev/public/images/4.png" width=50% height=50% alt= "Home Page">
<img src="https://github.com/Y2theK/chatty/blob/dev/public/images/5.png" width=50% height=50% alt= "Chat Page">
<img src="https://github.com/Y2theK/chatty/blob/dev/public/images/6.png" width=50% height=50% alt= "Search User">
<img src="https://github.com/Y2theK/chatty/blob/dev/public/images/3.png" width=50% height=50% alt= "Add User">



### Installation

```shell
git clone https://github.com/Y2theK/chatty.git
```

```shell
cp .env.example .env
```

```shell
Setup databases in .env file
```

```shell
composer install
```

```shell
php artisan key:generate
```

```shell
php artisan migrate:fresh --seed
```

```shell
npm install && npm run dev
```

```shell
php artisan serve
```

```shell
php artisan reverb:start
```

