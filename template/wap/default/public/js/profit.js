$(function(){
	if($('#container').length){
		var initEcharts = function(data,total){
			var dom = document.getElementById("container");
			var myChart = echarts.init(dom);
			var app = {},pieData = [];
			option = null;
			app.title = '环形图';
			option = {
			    tooltip: {
			        trigger: 'item',
			        formatter: "{a} <br/>{b}: {c} ({d}%)"
			    },
			    legend: {
			        orient: 'vertical',
			        x: 'left',
			        data:['直接访问','邮件营销','联盟广告','视频广告','搜索引擎']
			    },
				title: {
			        text:'￥'+(total*1).toFixed(2),
			        left:'center',
			        top:'45%',
			        textStyle:{
			          color:'#031f2d',
			          align:'center',
			          fontFamily:"san-serif",
			        }
			    },
		  		color:['#FF6333','#FF334E','#6048FF','#3194F7','#2DE09F','#FFCC33'], /*饼图颜色的调整*/
			    series: [
			        {
			            name:'月账单',
			            type:'pie',
			            radius: ['40%', '50%'],
			            avoidLabelOverlap: false,
			            hoverOffset:0,
			            label: {
			                normal: {
			                    show: true,
			                    fontSize:11,
			                    color: '#666',
		                        fontFamily:"Microsoft Yahei",
		                        formatter: "{b}: ({c})",
			                },
			                emphasis: {
			                    show: true,
			                    textStyle: {
			                        fontSize: '12',
			                        fontFamily:"san-serif",
			                        color:'#666'
			                    }
			                }
			            },
			            labelLine: {
			                normal: {
			                    show: true,
			                    length:5,
			                    length2:10,
			                }
			            },
			            data:pieData
			        }
			    ]
			};
			if(data == -2){
				pieData.push({value:0, name:'无消费'});
			}else{
				$.each(data,function(i,even){
					pieData.push({value:even.category_money, name:even.category_name});
				});
			}
			if (option && typeof option === "object") {
			    myChart.setOption(option, true);
			}
			$(window).on('resize',function(){
				myChart.resize();
			});
		},data = null;
		$.ajax({
			type:"post",
			data:{'data':0},
			url:APPMAIN +"/member/profit",
			success:function(msg){
				var html = ''
				for (var i = 0;i<msg.length;i++) {
					var k = i*1+1;
					var w = ((((msg[i].category_money)*1).toFixed(2))/(total_money*1).toFixed(2)) *100;
					html+='<li class="li'+k+' cf">'
						+'<div class="point fl"></div>'
						+'<div class="value fl cf">'
						+'<div class="name fl">'+msg[i].category_name+'</div>'
						+'<div class="line fl"><span style="width:'+w.toFixed(2)+'%"></span></div>'
						+'</div>'
						+'<div class="money fr">¥'+((msg[i].category_money)*1).toFixed(2)+'</div>'
						+'</li>'
				}
				initEcharts(msg,total_money);
				$('.liList').html(html);
			}
		});
	}
})
