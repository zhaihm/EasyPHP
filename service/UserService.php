<?php
class UserService {
    public static function GeneratePassword($password) {
        return substr(md5($password), 8, 16);
    }

    public static function GenerateUserId() {
        return intval(time()) << 16  | rand(0,30000);
    }
}

