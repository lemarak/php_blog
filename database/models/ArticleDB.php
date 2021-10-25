<?php

class ArticleDB
{

    private PDOStatement  $statementReadOne;
    private PDOStatement  $statementCreateOne;
    private PDOStatement  $statementUpdateOne;
    private PDOStatement  $statementDeleteOne;
    private PDOStatement  $statementReadAll;
    private PDOStatement $statementReadUserAll;

    function __construct(private PDO $pdo)
    {
        $this->statementReadOne = $pdo->prepare(
            'SELECT article.*, user.firstname, user.lastname 
            FROM article LEFT JOIN user 
            ON article.author = user.id 
            WHERE article.id=:id'
        );

        $this->statementReadAll = $pdo->prepare(
            'SELECT article.*, user.firstname, user.lastname 
            FROM article LEFT JOIN user 
            ON article.author = user.id'
        );

        $this->statementCreateOne = $pdo->prepare(
            'INSERT INTO article (
                title,
                category,
                content,
                image,
                author
                )
            VALUES (
                :title,
                :category,
                :content,
                :image,
                :author
            )'
        );

        $this->statementUpdateOne = $pdo->prepare(
            'UPDATE article 
            SET
                title=:title,
                category=:category,
                content=:content,
                image=:image,
                author=:author
            WHERE id=:id'
        );

        $this->statementDeleteOne = $pdo->prepare('DELETE FROM article WHERE id=:id');

        $this->statementReadUserAll = $pdo->prepare(
            'SELECT * FROM article
            WHERE author=:idAuthor'
        );
    }

    public function fetchAll(): array
    {
        $this->statementReadAll->execute();
        return $this->statementReadAll->fetchAll();
    }

    public function fetchOne(int $id): array
    {
        $this->statementReadOne->bindValue('id', $id);
        $this->statementReadOne->execute();
        return $this->statementReadOne->fetch();
    }

    public function deleteOne(int $id): string
    {
        $this->statementDeleteOne->bindValue(':id', $id);
        $this->statementDeleteOne->execute();
        return $id;
    }

    public function createOne($article)
    {
        $this->statementCreateOne->bindValue('title', $article['title']);
        $this->statementCreateOne->bindValue('image', $article['image']);
        $this->statementCreateOne->bindValue('category', $article['category']);
        $this->statementCreateOne->bindValue('content', $article['content']);
        $this->statementCreateOne->bindValue('author', $article['author']);

        $this->statementCreateOne->execute();
        $this->pdo->lastInsertId();
        return $this->fetchOne($this->pdo->lastInsertId());
    }

    public function updateOne($article): array
    {
        $this->statementUpdateOne->bindValue('title', $article['title']);
        $this->statementUpdateOne->bindValue('image', $article['image']);
        $this->statementUpdateOne->bindValue('category', $article['category']);
        $this->statementUpdateOne->bindValue('content', $article['content']);
        $this->statementUpdateOne->bindValue('author', $article['author']);
        $this->statementUpdateOne->bindValue('id', $article['id']);

        $this->statementUpdateOne->execute();
        return $article;
    }

    public function fetchUserArticle(string $idUser): array
    {
        $this->statementReadUserAll->bindValue(':idAuthor', $idUser);
        $this->statementReadUserAll->execute();
        return $this->statementReadUserAll->fetchAll();
    }
}

return new ArticleDB($pdo);
