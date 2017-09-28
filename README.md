#defender
09/20

	新增operaction模块

	system\modules\workorder\models\workorder	263行增加其他表的关联with

	system\modules\gateway\models\gateway	308-322行增加数据表的with方法实现

09/21

	tab_work_order表新增promise_time(承诺解决时间)、urge_num(催促次数)、order_time(工单时长)、problem(故障原因)字段
	
	system\modules\notify\components\notify		createMessage方法新增type参数、46行新增条件判断语句
	
	system\modules\notify\models\notify		新增TYPE_URGE属性、tab_notify表更新type字段对应注释
	
	system\modules\workorder\controllers\defaultController	新增urge方法、修改view查询语句新增with关联
	
	system\modules\workorder\models\workorder 	新增gateway、notify、urge关联
	
09/22
	
	完善urge方法、增加前台显示
	
	system\modules\workorder\controllers\defaultController  增加view方法查询数据量
	
09/25

	修改tab_user_gateway_group表，新增type字段，修改联合索引为user_id、target_id、type联合
	
	修改tab_gateway表，新增is_group字段，用于判断通过街道查找网关时，这个组下的网关是否显示
	
	修改system\modules\user\models\usergatewaygroup		getGroupsByUser、saveData、getUsersByGroup方法查询条件增加type
	
	修改system\modules\gateway\models\gateway			getUserByGateway方法修改查询字段

	testssssssssssss
	woshixiangyong
