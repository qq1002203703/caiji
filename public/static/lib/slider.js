    // ���캯����ԭ����ʽ

    /**
     * ���캯����ʼ������ʼ�ֲ�
     * @param bannnerBox string ���������ֲ�ͼ���ӵ�id��class
     * @param aBox  string ����ָʾ���ĺ��ӵ�id��class
     * @param btnBox string ����ǰ��ť�ĺ��ӵ�id��class
     */
    function Carousel(bannnerBox, aBox, btnBox) {
        this.now = 0; //��ǰ��ʾ��ͼƬ����
        this.hasStarted = false; //�Ƿ�ʼ�ֲ�
        this.interval = null; //��ʱ��
        this.liItems = null; //Ҫ�ֲ���liԪ�ؼ���
        this.len = 0; //liItems�ĳ���
        this.aBox = null; //����ָʾ����dom����
        this.bBox = null; //����ǰ��ť��dom����

        //��ʼ������
        this.init = function () {
            //��ʼ���������
            var that = this;
            this.liItems = $(bannnerBox).find('ul').find('li');
            this.len = this.liItems.length;
            this.aBox = $(bannnerBox).find(aBox);
            this.bBox = $(bannnerBox).find(btnBox);
            //�õ�һ��ͼƬ��ʾ�������ֲ�ͼ������̬����ָʾ�������õ�һ��ָʾ�����ڼ���״̬������ǰ��ť
            this.liItems.first('li').css({
                'opacity': 1,
                'z-index': 1
            }).siblings('li').css({
                'opacity': 0,
                'z-index': 0
            });
            var aDom = '';
            for (var i = 0; i < this.len; i++) {
                aDom += '<a></a>';
            }
            $(aDom).appendTo(this.aBox);
            this.aBox.find('a:first').addClass("indicator-active");
            this.bBox.hide();
            //�������fu-sliderͼʱ��ֹͣ�ֲ�����ʾǰ��ť���Ƴ�ʱ��ʼ�ֲ�������ǰ��ť
            $(bannnerBox).hover(function () {
                that.stop();
                that.bBox.fadeIn(200);
            }, function () {
                that.start();
                that.bBox.fadeOut(200);
            });
            //�������ָʾ��ʱ����ʾ��ӦͼƬ���Ƴ�ʱ��������
            this.aBox.find('a').hover(function () {
                that.stop();
                var out = that.aBox.find('a').filter('.indicator-active').index();
                that.now = $(this).index();
                if (out != that.now) {
                    that.play(out, that.now)
                }
            }, function () {
                that.start();
            });
            //������Ұ�ťʱ��ʾ��һ�Ż���һ��
            $(btnBox).find('a:first').click(function () {
                that.next()
            });
            $(btnBox).find('a:last').click(function () {
                that.prev()
            });
        }
        //��ʼ��
        this.init();
        //��ʼ�ֲ�
        this.start();
    }


    /**
     * ���ź���
     * @param out number Ҫ��ʧ��ͼƬ������ֵ
     * @param now number ������Ҫ�ֲ���ͼ������ֵ
     */
    Carousel.prototype.play = function (out, now) {
        this.liItems.eq(out).stop().animate({
            opacity: 0,
            'z-index': 0
        }, 500).end().eq(now).stop().animate({
            opacity: 1,
            'z-index': 1
        }, 500);
        this.aBox.find('a').removeClass('indicator-active').eq(now).addClass('indicator-active');
    }

    //ǰһ�ź���
    Carousel.prototype.prev = function () {
        var out = this.now;
        this.now = (--this.now + this.len) % this.len;
        this.play(out, this.now)
    }

    //��һ�ź���
    Carousel.prototype.next = function () {
        var out = this.now;
        this.now = ++this.now % this.len;
        this.play(out, this.now);
    }

    //��ʼ����
    Carousel.prototype.start = function () {
        if (!this.hasStarted) {
            this.hasStarted = true;
            var that = this;
            this.interval = setInterval(function () {
                that.next();
            }, 2000);
        }
    }
    //ֹͣ����
    Carousel.prototype.stop = function () {
        clearInterval(this.interval);
        this.hasStarted = false;
    }

    $(function () {
        var slider1 = new Carousel('#J_bg_ban1', '#J_bg_indicator1', '#J_bg_btn1');
    });