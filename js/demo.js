
$(function () {
    init_ctl.myChart = echarts.init($('#basic')[0]);
})

var init_ctl = {
    myChart: null,
    calc: function () {
        // 后台交互
        $.ajax({
            url: "./basic.php?action=calc",
            type: "POST",
            dataType: "json",
            data: $("form").serialize(),
            success: function( result ) {
                if (result.success) {
                    init_ctl.calc_basic(
                        result.request.cash,
                        result.request.percent * 0.01,
                        result.params,
                        result.statistics
                    )
                    $('#main').hide()
                    $('#result').show()
                }
                else {
                    $('#alertMessage').html(result.message)
                    $('#alert').modal('show')
                }
            }
        });
    },
    get_person_tax: function (cash, tax_table) {
        var person_tax = 0
        for(const tax in tax_table) {
            if (cash < tax_table[tax].tax) {
                continue
            }
            person_tax += (cash - tax_table[tax].tax) * tax_table[tax].percent

            console.log(cash, tax_table[tax].tax, person_tax)
            cash = tax_table[tax].tax
        }
        return person_tax
    },
    calc_basic: function (cash, percent, params, statistics) {
        // 基础部分
        $('#cash').html(cash + ' &yen;')
        $('#percent').html(percent * 100 + '%')
        var person_tax = this.get_person_tax(cash, params.tax_table)
        $('#person_tax').html(person_tax.toFixed(2) + ' &yen;')
        var person_house_fund = cash * percent
        $('#person_house_fund').html(person_house_fund.toFixed(2) + ' &yen;')

        // 公积金分解
        var person_pension = cash * params.pension.person
        $('#person_pension').html(person_pension.toFixed(2) + ' &yen;')
        var person_care = cash * params.care.person
        $('#person_care').html(person_care.toFixed(2) + ' &yen;')
        $('#person_house').html(person_house_fund.toFixed(2) + ' &yen;')
        var company_pension = cash * params.pension.company
        $('#company_pension').html(company_pension.toFixed(2) + ' &yen;')
        var company_care = cash * params.care.company
        $('#company_care').html(company_care.toFixed(2) + ' &yen;')
        var company_house_fund = cash * percent
        $('#company_house').html(company_house_fund.toFixed(2) + ' &yen;')

        // 实际工资
        var money = cash - person_tax - person_house_fund - person_pension - person_care
        console.log(money)
        $('#person_result_cash').html(money.toFixed(2) + ' &yen;')

        var data = [
            {
                value: person_tax.toFixed(2),
                name: '个人所得税'
            },
            {
                value: person_house_fund.toFixed(2),
                name: '个人公积金'
            },
            {
                value: person_pension.toFixed(2),
                name: '个人养老金'
            },
            {
                value: person_care.toFixed(2),
                name: '个人医疗保险'
            },
            {
                value: money.toFixed(2),
                name: '实际工资'
            }
        ]
        this.init_basic_bing(data)

        var about_cash = statistics.overCashCount < statistics.funcCount * params.staticsist ? '靠前':
            (statistics.overCashCount < statistics.funcCount * ( 1 - params.staticsist) ? '靠中' : '靠后')
        $('#about_cash').html(about_cash)
        var about_industry = statistics.overAvgIndustry < statistics.totalIndustry * params.staticsist ? '靠前':
            (statistics.overAvgIndustry < statistics.totalIndustry * ( 1 - params.staticsist) ? '靠中' : '靠后')
        $('#about_industry').html(about_industry)
        var about_year = ''
        // 重构返回前台数据
        if (statistics.lastYear == 0 || statistics.thisYear == 0) {
            about_year = '数据过少无法统计';
        }
        else if (statistics.lastYear <= statistics.thisYear) {
            var year_percent = ((statistics.thisYear-statistics.lastYear)/statistics.lastYear).toFixed(2)
            about_year = '高 {percent}%'.replace('{percent}', year_percent)
        }
        else if ($lastYear > $thisYear) {
            var year_percent = ((statistics.lastYear-statistics.thisYear)/statistics.lastYear).toFixed(2)
            about_year = '低 {percent}%'.replace('{percent}', year_percent)
        }
        $('#about_year').html(about_year)
    },
    init_basic_bing: function (data) {
        // 导入饼图数据
        basic_option.series[0].data = data.sort(function (a, b) { return a.value - b.value; })
        // 使用刚指定的配置项和数据显示图表。
        this.myChart.setOption(basic_option);
    }

}

// 饼图参数
var basic_option = {
    data: [],
    tooltip : {
        trigger: 'item',
        formatter: "{b} : {c} ({d}%)"
    },
    series : [
        {
            name:'基础信息',
            type:'pie',
            radius : '55%',
            center: ['50%', '50%'],
            labelLine: {
                normal: {
                    smooth: 0.2,
                    length: 10,
                    length2: 20
                }
            },
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },

            animationType: 'scale',
            animationEasing: 'elasticOut',
            animationDelay: function (idx) {
                return Math.random() * 200;
            }
        }
    ]
};
