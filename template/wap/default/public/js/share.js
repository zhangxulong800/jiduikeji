$.ajax({
    type:"post",
    url : "APP_MAIN/member/getShareContents",
    data : {"shop_id" : "{$shop_id}" , "flag" : "goods" , "goods_id" : "{$goods_id}"},
    success : function(data){
        console.log('=======',data)
        var data = data;
        $.ajax({
            type:"post",
            url : "APP_MAIN/share/index",
            success : function(data){
                wx.config({
                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: data.appId, // 必填，公众号的唯一标识
                    timestamp: data.timestamp, // 必填，生成签名的时间戳
                    nonceStr:  data.noncestr, // 必填，生成签名的随机串
                    signature: data.sign,// 必填，签名，见附录1
                    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                });
                wx.ready(function() {
                    var title = data['share_title'];
                    var share_contents = data['share_contents']+'\r\n';
                    var share_nick_name = data['share_nick_name']+'\r\n';
                    var desc = share_contents+ share_nick_name + "收藏热度：★★★★★";
                    var url = data['share_url'];
                    var imgurl = data['share_img'];
                    wx.onMenuShareAppMessage({
                        title: title,
                        desc: desc,
                        link: url,
                        imgUrl: imgurl,
                        trigger: function (res) {
                            //alert('用户点击发送给朋友');
                        },
                        success: function () {
                            //alert('分享成功');
                            /*$.ajax({
                                type : "post",
                                url : "APP_MAIN/index/sharegivepoint",
                                data : {
                                    "share" : true
                                },
                                success : function(data){

                                }
                            });*/
                        },
                        cancel: function (res) {
                            //alert('已取消');
                        },
                        fail: function (res) {
                            //alert(JSON.stringify(res));
                        }
                    });

                    // 2.2 监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
                    wx.onMenuShareTimeline({
                        title: title,
                        link: url,
                        imgUrl: imgurl,
                        trigger: function (res) {
                            // alert('用户点击分享到朋友圈');
                        },
                        success: function (res) {
                            //alert('已分享');
                            /*$.ajax({
                                type : "post",
                                url : "APP_MAIN/index/sharegivepoint",
                                data : {
                                    "share" : true
                                },
                                success : function(data){

                                }
                            });*/
                        },
                        cancel: function (res) {
                            //alert('已取消');
                        },
                        fail: function (res) {
                            // alert(JSON.stringify(res));
                        }
                    });

                    // 2.3 监听“分享到QQ”按钮点击、自定义分享内容及分享结果接口
                    wx.onMenuShareQQ({
                        title: title,
                        desc: desc,
                        link: url,
                        imgUrl: imgurl,
                        trigger: function (res) {
                            //alert('用户点击分享到QQ');
                        },
                        complete: function (res) {
                            //alert(JSON.stringify(res));
                        },
                        success: function (res) {
                            //alert('已分享');
                            /*$.ajax({
                                type : "post",
                                url : "APP_MAIN/index/sharegivepoint",
                                data : {
                                    "share" : true
                                },
                                success : function(data){

                                }
                            });*/
                        },
                        cancel: function (res) {
                            //alert('已取消');
                        },
                        fail: function (res) {
                            //alert(JSON.stringify(res));
                        }
                    });

                    // 2.4 监听“分享到微博”按钮点击、自定义分享内容及分享结果接口
                    wx.onMenuShareWeibo({
                        title: title,
                        desc: desc,
                        link: url,
                        imgUrl: imgurl,
                        trigger: function (res) {
                            //alert('用户点击分享到微博');
                        },
                        complete: function (res) {
                            //alert(JSON.stringify(res));
                        },
                        success: function (res) {
                            //alert('已分享');
                            /*$.ajax({
                                type : "post",
                                url : "APP_MAIN/index/sharegivepoint",
                                data : {
                                    "share" : true
                                },
                                success : function(data){

                                }
                            });*/
                        },
                        cancel: function (res) {
                            //alert('已取消');
                        },
                        fail: function (res) {
                            //alert(JSON.stringify(res));
                        }
                    });
                });

            }
        })
    }
})