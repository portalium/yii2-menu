<?php

namespace portalium\menu\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use portalium\menu\models\Menu;

/**
 * MenuSearch represents the model behind the search form of `portalium\menu\models\Menu`.
 */
class MenuRouteSearch extends Menu
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_menu_route'], 'integer'],
            [['title', 'route', 'type', 'module', 'date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MenuRoute::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_menu_route' => $this->id_menu,
            'route' => $this->route,
            'type' => $this->type,
            'module' => $this->module,
            'date_create' => $this->date_create,
            'date_update' => $this->date_update,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'title', $this->title]);


        return $dataProvider;
    }
}
