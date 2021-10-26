<?php

class AuthDB
{
    private PDOStatement $statementRegister;
    private PDOStatement $statementReadSession;
    private PDOStatement $statementReadUser;
    private PDOStatement $statementReadUserFromEmail;
    private PDOStatement $statementCreateSession;
    private PDOStatement $statementDeleteSession;

    function __construct(private PDO $pdo)
    {
        $this->statementRegister = $this->pdo->prepare('INSERT INTO user VALUES (
            DEFAULT,
            :firstname,
            :lastname,
            :email,
            :password
            )');

        $this->statementReadSession = $this->pdo->prepare(
            'SELECT * FROM session
            WHERE id=:id'
        );

        $this->statementReadUser = $this->pdo->prepare(
            'SELECT * FROM user
        WHERE id=:id'
        );

        $this->statementReadUserFromEmail = $this->pdo->prepare(
            'SELECT * FROM user WHERE email=:email'
        );

        $this->statementCreateSession = $this->pdo->prepare(
            'INSERT INTO session VALUES (
            :sessionId,
            :idUser
            )'
        );

        $this->statementDeleteSession = $pdo->prepare(
            'DELETE FROM session
            WHERE id=:id'
        );
    }

    function getUserFromEmail(string $email): array
    {
        $this->statementReadUserFromEmail->bindValue(':email', $email);
        $this->statementReadUserFromEmail->execute();
        return $this->statementReadUserFromEmail->fetch();
    }

    function login(string $idUser): void
    {
        $sessionId = bin2hex(random_bytes(32));
        $this->statementCreateSession->bindValue(':sessionId', $sessionId);
        $this->statementCreateSession->bindValue(':idUser', $idUser);
        $this->statementCreateSession->execute();
        $signature = hash_hmac('sha256', $sessionId, 'trois petits chats');
        setCookie('session', $sessionId, time() + 3600 * 24 * 2, '', '', false, true);
        setCookie('signature', $signature, time() + 3600 * 24 * 2, '', '', false, true);
        return;
    }

    function register(array $user): void
    {
        $hashPassword = password_hash($user['password'], PASSWORD_ARGON2I);
        $this->statementRegister->bindValue(':firstname', $user['firstname']);
        $this->statementRegister->bindValue(':lastname', $user['lastname']);
        $this->statementRegister->bindValue(':email', $user['email']);
        $this->statementRegister->bindValue(':password', $hashPassword);
        $this->statementRegister->execute();
        return;
    }

    function isLoggedIn(): array | false
    {
        $sessionId = $_COOKIE['session'] ?? '';
        $signature = $_COOKIE['signature'] ?? '';

        if ($sessionId && $signature) {
            $hash = hash_hmac('sha256', $sessionId, 'trois petits chats');
            if (hash_equals($hash, $signature)) {
                $this->statementReadSession->bindValue(':id', $sessionId);
                $this->statementReadSession->execute();
                $session = $this->statementReadSession->fetch();
                if ($session) {
                    $this->statementReadUser->bindValue(':id', $session['iduser']);
                    $this->statementReadUser->execute();
                    $user = $this->statementReadUser->fetch();
                }
            }
        }
        return $user ?? false;
    }

    function logOut(string $sessionId): void
    {
        $this->statementDeleteSession->bindValue(':id', $sessionId);
        $this->statementDeleteSession->execute();
        setcookie('session', '', time() - 1);
        setcookie('signature', '', time() - 1);
    }
}

return new AuthDB($pdo);
