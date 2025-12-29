<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

// Підключаємо наші моделі
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm; // Переконайтеся, що створили цю модель, або видаліть actionSignup
use app\models\Article;
use app\models\Topic;
use app\models\CommentForm;
use app\models\User;
use app\models\Vote;

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
     * Сторінка перегляду однієї поради
     */
    public function actionView($id)
    {
        $article = Article::findOne($id);

        // Якщо статтю не знайдено - помилка 404
        if (!$article) {
            throw new NotFoundHttpException("The requested article does not exist.");
        }

        // Збільшуємо лічильник переглядів
        $article->viewed += 1;
        $article->save(false);

        // Отримуємо коментарі (активні)
        $comments = $article->getComments()->where(['delete' => 0])->all();

        // Форма для нового коментаря
        $commentForm = new CommentForm();

        // Дані для сайдбару
        $popular = Article::find()->orderBy(['upvotes' => SORT_DESC])->limit(3)->all();
        $recent = Article::find()->orderBy(['date' => SORT_DESC])->limit(3)->all();
        $topics = Topic::find()->all();

        return $this->render('single', [
            'article' => $article,
            'comments' => $comments,
            'commentForm' => $commentForm,
            'popular' => $popular,
            'recent' => $recent,
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
            if ($model->saveComment($id)) {
                Yii::$app->session->setFlash('success', "Comment posted successfully!");
                return $this->redirect(['site/view', 'id' => $id]);
            }
        }

        return $this->redirect(['site/view', 'id' => $id]);
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
        $model = new SignupForm(); // Переконайтеся, що створили models/SignupForm.php

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
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
