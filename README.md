# Chatty

#### Descript
Chatty is small a real time messaging app providing seemless experience of chatting between friends and families. It includes features like messaging in private chat/ group chat, creating conversation, inviting new user to group, leave conversation, online/ offline user status, current active users in groups and even whisper typing event.

### Tech Stacks:
`Laravel 11` `sqlite` `vue 3` `laravel-reverb` `tailwindcss` `shadcn-vue` `inertia` `axios` `laravel-breeeze`

### Coming soon
- Support Image, Video, emoji and links in conversation
- Authorization in group and private chats
- Message delete and reply
- Status Sharing
- Profile Image Upload

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

