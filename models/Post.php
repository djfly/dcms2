<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Tag;
use app\models\Lookup;
use app\components\Common;
use app\models\Comment;
use app\models\Category;
use yii\helpers\Html;
use yii\imagine\image;
use app\models\Source;

/**
 * This is the model class for table "tbl_post".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $title
 * @property string $type
 * @property string $thumbnail
 * @property string $url
 * @property string $summary
 * @property string $source
 * @property string $writer
 * @property string $content
 * @property string $tags
 * @property string $seo_title
 * @property string $seo_keywords
 * @property string $seo_description
 * @property string $published_at
 * @property integer $views
 * @property integer $likes
 * @property integer $comment_count
 * @property integer $disallow_comment
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class Post extends \yii\db\ActiveRecord
{
    public $makeSummary;
    private $_oldTags;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'blameable' =>[
                'class' => 'yii\behaviors\BlameableBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['category_id', 'disallow_comment', 'status', 'likes'], 'integer'],
            [['content'], 'string'],
            [['title', 'thumbnail', 'source', 'writer', 'url', 'summary', 'tags', 'seo_title', 'seo_keywords', 'seo_description', 'type'], 'string', 'max' => 255],
            [['published_at'], 'default', 'value'=>''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Category'),
            'title' => Yii::t('app', 'Title'),
            'type' => Yii::t('app', 'Type'),
            'thumbnail' => Yii::t('app', 'Thumbnail'),
            'url' => Yii::t('app', 'Url'),
            'summary' => Yii::t('app', 'Summary'),
            'source' => Yii::t('app', 'Source'),
            'writer' => Yii::t('app', 'Writer'),
            'content' => Yii::t('app', 'Content'),
            'tags' => Yii::t('app', 'Tags'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_keywords' => Yii::t('app', 'Seo Keywords'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'published_at' => Yii::t('app', 'Published At'),
            'views' => Yii::t('app', 'Views'),
            'likes' => Yii::t('app', 'Likes'),
            'comment_count' => Yii::t('app', 'Comment Count'),
            'disallow_comment' => Yii::t('app', 'Disallow Comment'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getSourceUrl()
    {
        if (!empty($this->source)) {
            $source = Source::findOne(['name' => $this->source]);
            if (!empty($source->url)) {
                return $source->url;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function getMakeSummary()
    {
        if (empty($this->summary)) {
            return mb_substr(preg_replace('[\s\p{Zs}|&nbsp;|\n\s*\r]', '',strip_tags($this->content)), 0, 180, 'utf-8'); 
        }else{
            return $this->summary;
        }
    }

    public function getMakeTime()
    {
        return Common::formatTime($this->published_at);
    }

    public function getAboutRead()
    {
        if ($this->tags) {
            $tags=explode(',', $this->tags);
            $posts=[];
            foreach ($tags as $key => $value) {
                $posts=array_merge($posts,$this->find()->where(['category_id' => $this->category_id])->andWhere(['like', 'tags', $value])->limit(5)->asArray()->all());
                $posts=array_map("unserialize", array_unique(array_map("serialize", $posts)));
                if (count($posts)>=5) {
                    break;
                }
            }
            return array_slice($posts, 0, 5);
        }
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return array a list of links that point to the post list filtered by every tag of this post
     */
    public function getTagLinks()
    {
        $links=[];
        foreach(Tag::string2array($this->tags) as $tag)
        {
             $links[]=Html::a(Html::encode($tag), ['post/index', 'tag'=>$tag]);
        }
           
        return $links;
    }

    /**
     * Normalizes the user-entered tags.
     */
    public function normalizeTags($attribute,$params)
    {
        $this->tags = Tag::array2string(array_unique(Tag::string2array($this->tags)));
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            if (!empty($this->tags)) {
                $this->tags = str_replace("，", ",", $this->tags);
            }

            if(!empty($this->published_at)){
                $this->published_at = strtotime($this->published_at);
            }else{
               $this->published_at = time(); 
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->_oldTags = $this->tags;
    }

    public function afterSave($insert)
    {
        parent::afterSave($insert);
        $tag= new Tag;
        $tag->updateFrequency($this->_oldTags, $this->tags);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        $Comment = new Comment;
        $Comment->deleteAll(['post_id' => $this->id]);
        $tag = new Tag;
        $tag->updateFrequency($this->tags, '');
    }
}
