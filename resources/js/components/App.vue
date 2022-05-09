<template>
    <div class="wrapper" id="app">
        <div class="player">
            <div class="player__top">
                <div class="player-cover">
                    <transition-group :name="transitionName">
                        <div class="player-cover__item" v-if="$index === currentTrackIndex"
                             :style="{ backgroundImage: `url(${track.cover})` }" v-for="(track, $index) in tracks"
                             :key="$index"></div>
                    </transition-group>
                </div>
                <div class="player-controls">
                    <div class="player-controls__item -favorite">
                        <svg class="icon">

                        </svg>
                    </div>
                    <a :href="currentTrack.url" target="_blank" class="player-controls__item">
                        <svg class="icon">
                            <use xlink:href="#icon-link"></use>
                        </svg>
                    </a>
                    <div class="player-controls__item" @click="prevTrack">
                        <svg class="icon">
                            <use xlink:href="#icon-prev"></use>
                        </svg>
                    </div>
                    <div class="player-controls__item" @click="nextTrack">
                        <svg class="icon">
                            <use xlink:href="#icon-next"></use>
                        </svg>
                    </div>
                    <div class="player-controls__item -xl js-play" @click="play">
                        <svg class="icon">
                            <use xlink:href="#icon-pause" v-if="isTimerPlaying"></use>
                            <use xlink:href="#icon-play" v-else></use>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="progress" ref="progress">
                <div class="progress__top">
                    <div class="album-info" v-if="currentTrack">
                        <div class="album-info__name">{{ currentTrack.artist }}</div>
                        <div class="album-info__track">{{ currentTrack.name }}</div>
                    </div>
                    <div class="progress__duration">{{ duration }}</div>
                </div>
                <div class="progress__bar" @click="clickProgress">
                    <div class="progress__current" :style="{ width : barWidth }"></div>
                </div>
                <div class="progress__time">{{ currentTime }}</div>
            </div>
            <div v-cloak></div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            audio: null,
            circleLeft: null,
            barWidth: null,
            duration: null,
            currentTime: null,
            isTimerPlaying: false,
            tracks: [],
            currentTrack: null,
            currentTrackIndex: 0,
            transitionName: null,
        };
    },
    methods: {
        play() {
            if (this.audio.paused) {
                this.audio.play();
                this.isTimerPlaying = true;
            } else {
                this.audio.pause();
                this.isTimerPlaying = false;
            }
        },
        generateTime() {
            let width = (100 / this.audio.duration) * this.audio.currentTime;
            this.barWidth = width + "%";
            this.circleLeft = width + "%";
            let durmin = Math.floor(this.audio.duration / 60);
            let dursec = Math.floor(this.audio.duration - durmin * 60);
            let curmin = Math.floor(this.audio.currentTime / 60);
            let cursec = Math.floor(this.audio.currentTime - curmin * 60);
            if (durmin < 10) {
                durmin = "0" + durmin;
            }
            if (dursec < 10) {
                dursec = "0" + dursec;
            }
            if (curmin < 10) {
                curmin = "0" + curmin;
            }
            if (cursec < 10) {
                cursec = "0" + cursec;
            }
            this.duration = durmin + ":" + dursec;
            this.currentTime = curmin + ":" + cursec;
        },
        updateBar(x) {
            let progress = this.$refs.progress;
            let maxduration = this.audio.duration;
            let position = x - progress.offsetLeft;
            let percentage = (100 * position) / progress.offsetWidth;
            if (percentage > 100) {
                percentage = 100;
            }
            if (percentage < 0) {
                percentage = 0;
            }
            this.barWidth = percentage + "%";
            this.circleLeft = percentage + "%";
            this.audio.currentTime = (maxduration * percentage) / 100;
            this.audio.play();
        },
        clickProgress(e) {
            this.isTimerPlaying = true;
            this.audio.pause();
            this.updateBar(e.pageX);
        },
        prevTrack() {
            this.transitionName = "scale-in";
            this.isShowCover = false;
            if (this.currentTrackIndex > 0) {
                this.currentTrackIndex--;
            } else {
                this.currentTrackIndex = this.tracks.length - 1;
            }
            this.currentTrack = this.tracks[this.currentTrackIndex];
            this.resetPlayer();
        },
        nextTrack() {
            this.transitionName = "scale-out";
            this.isShowCover = false;
            if (this.currentTrackIndex < this.tracks.length - 1) {
                this.currentTrackIndex++;
            } else {
                this.currentTrackIndex = 0;
            }
            this.currentTrack = this.tracks[this.currentTrackIndex];
            this.resetPlayer();
        },
        resetPlayer() {
            this.barWidth = 0;
            this.circleLeft = 0;
            this.audio.currentTime = 0;
            this.audio.src = this.currentTrack.source;
            setTimeout(() => {
                if (this.isTimerPlaying) {
                    this.audio.play();
                } else {
                    this.audio.pause();
                }
            }, 300);
        },

    },
    created() {
        try {
            let vm = this;
            axios.get("/api/getMusicList").then(async response => {
                if (response.data.code === 200) {
                    vm.tracks = response.data.data
                    vm.audio = new Audio();
                    vm.currentTrack = vm.tracks[0];
                    vm.audio.src = vm.currentTrack.source;

                    vm.audio.onerror = function () {
                        vm.tracks.splice(vm.tracks.indexOf(vm.currentTrack), 1);
                        vm.currentTrack=vm.tracks[vm.currentTrackIndex]
                        vm.resetPlayer()
                    }

                    vm.audio.ontimeupdate = function () {
                        vm.generateTime();
                    };
                    vm.audio.onloadedmetadata = function () {
                        vm.generateTime();
                    };
                    vm.audio.onended = function () {
                        vm.nextTrack();
                        vm.isTimerPlaying = true;
                    };
                    for (let index = 0; index < vm.tracks.length; index++) {
                        const element = vm.tracks[index];
                        let link = document.createElement('link');
                        link.rel = "prefetch";
                        link.href = element.cover;
                        link.as = "image"
                        document.head.appendChild(link)
                    }
                    for (let i = 0; i < this.tracks.length; i++) {
                        await axios.get("/api/getMusicInfo/" + this.tracks[i].id).then(response => {
                            if (response.data.code === 200) {
                                this.tracks[i].cover = response.data.data.cover
                                this.tracks[i].artist = response.data.data.artist
                            } else {
                                this.tracks.splice(i, 1);
                                i--;
                            }
                        })
                    }
                }
            }).catch(error => {
                console.log(error)
            })
        } catch (e) {
            console.log(e)
        }
    }
}
</script>

<style scoped>

</style>
