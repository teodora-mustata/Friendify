<?php
interface Authenticable {
    public function register($username, $email, $password);
    public function login($username, $password);
    public function logout();
}
?>
