<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\Cookie;

// Підключаємо наші моделі
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm; // Переконайтеся, що створили цю модель, або видаліть actionSignup
use app\models\Article;
use app\models\Topic;
use app\models\CommentForm;
use app\models\User;
use app\models\Vote;
use app\models\Comment;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'comment'], // Дії, доступні тільки авторизованим
                'rules' => [
                    [
                        'actions' => ['logout', 'comment'],
                        'allow' => true,
                        'roles' => ['@'], // @ означає "авторизований користувач"
                    ],
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'], // ? означає "гість"
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Головна сторінка: Виведення списку порад з пагінацією
     */
    public function actionIndex()
    {
        // Формуємо запит на отримання всіх статей
        $query = Article::find();

        // Створюємо пагінацію (по 5 статей на сторінку)
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 5]);

        // Отримуємо статті для поточної сторінки, відсортовані за датою (спочатку нові)
        $articles = $query->orderBy(['date' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        // Отримуємо дані для сайдбару (популярне та категорії)
        $popular = Article::find()->orderBy(['upvotes' => SORT_DESC])->limit(3)->all();
        $recent = Article::find()->orderBy(['date' => SORT_DESC])->limit(3)->all();
        $topics = Topic::find()->all();

        return $this->render('index', [
            'articles' => $articles,
            'pagination' => $pagination,
            'popular' => $popular,
            'recent' => $recent,
            'topics' => $topics,
        ]);
    }

    /**
     * Перегляд однієї статті.
     * Реалізовано захист від накрутки переглядів (1 перегляд на 24 години для користувача).
     */
    public function actionView($id)
    {
        // 1. Знаходимо статтю в базі даних
        $article = Article::findOne($id);

        if (!$article) {
            throw new \yii\web\NotFoundHttpException("Article not found.");
        }

        // 2. ЛОГІКА УНІКАЛЬНОГО ПЕРЕГЛЯДУ (через Cookies)
        // Перевіряємо, чи є у користувача кука, що він вже бачив цю статтю
        $cookiesRequest = Yii::$app->request->cookies;
        $cookieName = 'viewed_article_' . $id;

        if (!$cookiesRequest->has($cookieName)) {
            // Якщо куки немає:
            // А) Збільшуємо лічильник у БД
            $article->updateCounters(['viewed' => 1]);

            // Б) Записуємо куку користувачу на 24 години (86400 секунд)
            $cookiesResponse = Yii::$app->response->cookies;
            $cookiesResponse->add(new \yii\web\Cookie([
                'name' => $cookieName,
                'value' => '1',
                'expire' => time() + 86400, // Час життя: поточний час + 1 доба
            ]));
        }

        // 3. Отримуємо коментарі (активні)
        $comments = $article->getComments()->all();

        // 4. Модель для форми нового коментаря
        $commentForm = new CommentForm();

        // 5. Дані для сайдбару (Популярні пости сортуємо за лайками)
        $popular = Article::find()->orderBy(['upvotes' => SORT_DESC])->limit(3)->all();
        $topics = Topic::find()->all();

        // 6. Відображаємо вигляд
        return $this->render('single', [
            'article' => $article,
            'comments' => $comments,
            'commentForm' => $commentForm,
            'popular' => $popular,
            'topics' => $topics,
        ]);
    }

    /**
     * Перегляд статей певної категорії
     */
    public function actionTopic($id)
    {
        $topic = Topic::findOne($id);

        if (!$topic) {
            throw new NotFoundHttpException("Category not found.");
        }

        $query = Article::find()->where(['topic_id' => $id]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 5]);

        $articles = $query->orderBy(['date' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $popular = Article::find()->orderBy(['upvotes' => SORT_DESC])->limit(3)->all();
        $recent = Article::find()->orderBy(['date' => SORT_DESC])->limit(3)->all();
        $topics = Topic::find()->all();

        return $this->render('topic', [
            'articles' => $articles,
            'pagination' => $pagination,
            'topic' => $topic,
            'popular' => $popular,
            'recent' => $recent,
            'topics' => $topics,
        ]);
    }

    /**
     * Пошук (за тегами або назвою) - Optional Requirement from Methodics
     */
    public function actionSearch($q = null)
    {
        $query = Article::find();

        if ($q) {
            // Шукаємо в назві, описі або тегах
            $query->where(['like', 'title', $q])
                ->orWhere(['like', 'description', $q])
                ->orWhere(['like', 'tag', $q]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 5]);

        $articles = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $popular = Article::find()->orderBy(['upvotes' => SORT_DESC])->limit(3)->all();
        $recent = Article::find()->orderBy(['date' => SORT_DESC])->limit(3)->all();
        $topics = Topic::find()->all();

        return $this->render('search', [ // Потрібно буде створити файл search.php (копія index.php)
            'articles' => $articles,
            'pagination' => $pagination,
            'q' => $q,
            'popular' => $popular,
            'recent' => $recent,
            'topics' => $topics,
        ]);
    }

    /**
     * Обробка додавання коментаря
     */
    public function actionComment($id)
    {
        $model = new CommentForm();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {

                $comment = new Comment();
                $comment->article_id = $id;
                $comment->user_id = Yii::$app->user->id;
                $comment->text = $model->comment;
                $comment->date = date('Y-m-d');

                // Якщо є parentId, зберігаємо його
                if (!empty($model->parentId)) {
                    $comment->parent_id = $model->parentId;
                }

                $comment->save();

                Yii::$app->session->setFlash('success', "Comment added successfully");
                return $this->redirect(['site/view', 'id' => $id]);
            }
        }
    }

    /**
     * Повне видалення коментаря з бази
     */
    public function actionDeleteComment($id)
    {
        $comment = Comment::findOne($id);

        // Перевірка прав: чи це автор коментаря?
        if (!$comment || Yii::$app->user->isGuest || $comment->user_id != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException("You cannot delete this comment.");
        }

        // --- БУЛО (Soft Delete) ---
        // $comment->delete = 1;
        // $comment->save(false);

        // --- СТАЛО (Hard Delete) ---
        // Цей метод фізично видаляє запис з SQL.
        // Завдяки CASCADE у базі, всі відповіді (діти) теж зникнуть автоматично.
        $comment->delete();

        Yii::$app->session->setFlash('success', "Comment deleted.");
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Редагування коментаря
     */
    public function actionUpdateComment($id)
    {
        $comment = Comment::findOne($id);

        // Перевірка прав
        if (!$comment || Yii::$app->user->isGuest || $comment->user_id != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException("You cannot edit this comment.");
        }

        // Використовуємо CommentForm для валідації тексту
        $model = new CommentForm();

        if ($model->load(Yii::$app->request->post())) {
            // Оновлюємо текст
            $comment->text = $model->comment;
            // Ставимо позначку "Змінено"
            $comment->is_edited = 1;

            if ($comment->save()) {
                Yii::$app->session->setFlash('success', "Comment updated.");
                return $this->redirect(['site/view', 'id' => $comment->article_id]);
            }
        }

        // Заповнюємо форму поточним текстом
        $model->comment = $comment->text;

        return $this->render('edit-comment', [
            'model' => $model,
            'comment' => $comment
        ]);
    }

    /**
     * Login action.
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Signup action (Реєстрація)
     */
    public function actionSignup()
    {
        $model = new SignupForm(); // Або ваша модель форми реєстрації

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $user = new User();
            $user->name = $model->name;
            $user->email = $model->email;

            // ВАЖЛИВО: Використовуємо метод setPassword замість прямого присвоєння
            // БУЛО: $user->password = $model->password;
            // СТАЛО:
            $user->setPassword($model->password);

            if ($user->save()) {
                return $this->redirect(['/site/login']);
            }
        }

        return $this->render('signup', ['model' => $model]);
    }

    /**
     * Like / Unlike action
     */
    public function actionLike($id)
    {
        // Тільки для авторизованих
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }

        $article = Article::findOne($id);
        if (!$article) {
            return $this->redirect(['index']);
        }

        $currentUser = Yii::$app->user->id;

        // Перевіряємо, чи вже є лайк
        $vote = Vote::find()->where(['user_id' => $currentUser, 'article_id' => $id])->one();

        if ($vote) {
            // Якщо є - видаляємо (дизлайк) і зменшуємо лічильник
            $vote->delete();
            $article->updateCounters(['upvotes' => -1]);
        } else {
            // Якщо немає - створюємо (лайк) і збільшуємо лічильник
            $vote = new Vote();
            $vote->user_id = $currentUser;
            $vote->article_id = $id;
            $vote->save();
            $article->updateCounters(['upvotes' => 1]);
        }

        // Повертаємо користувача на сторінку, з якої він прийшов
        return $this->redirect(Yii::$app->request->referrer);
    }
}
