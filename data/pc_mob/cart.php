<?php
$cart_list = $goods->getCart($this->uid);
foreach($cart_list as $key=>$val){
	if($val['point_exchange_type']==2){
		$point_info = $cf->getInfo(['shop_id' => 0],'convert_rate');//$val['shop_id']改成0号店铺
		if($point_info['convert_rate']>0){
			$cart_list[$key]['point_exchange']=round($val['price']/$point_info['convert_rate'],2);
			$cart_list[$key]['price']=0.00;
		}
	}
}
// 店铺，店铺中的商品
$list = Array();
for ($i = 0; $i < count($cart_list); $i ++) {
	$list[$cart_list[$i]["shop_id"] . ',' . $cart_list[$i]["shop_name"]][] = $cart_list[$i];
}