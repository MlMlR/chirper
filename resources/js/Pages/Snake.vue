<script setup>
import { ref, onMounted } from 'vue'

// State
const BUG_SPEED = 6
const SNAKE_SPEED = 6
let bugExists = ref(false)
const bugBody = ref(randomGridPosition())
const gameBoard = ref(document.getElementById('game-board'))
let gameOver = ref(false)
let gameOverMessage = ref('GAME OVER')
const GRID_SIZE = 21
let inputDirection = ref({x: 0, y: -1})
let lastInputDirection = ref({x: 0, y: 0})
let bugInputDirection = ref({x: 1, y: 0})
let lastBugDirection = ref({x: 0, y: 0})
let lastRenderTime = ref(0)
let food = [
    getRandomFoodPosition(),
    getRandomFoodPosition()
]
const EXPANSION_RATE = 5
const snakeBody = [
    {x:11, y:11},
    {x:11, y:12},
    {x:11, y:13},
    {x:11, y:14},
    {x:11, y:15}
]
let newSegments = 0

// Game logic functions
function drawBug(gameBoard) {
    const bugElement = document.createElement('div')
    bugElement.style.gridRowStart = bugBody.y
    bugElement.style.gridColumnStart = bugBody.x
    bugElement.classList.add('bug')
    gameBoard.appendChild(bugElement)

}

function randomGridPosition() {
    return {
        x: Math.floor(Math.random() * GRID_SIZE) + 1,
        y: Math.floor(Math.random() * GRID_SIZE) + 1
    }
}

function outsideGrid(position){
    return (
        position.x < 1 || position.x > GRID_SIZE ||
        position.y < 1 || position.y > GRID_SIZE
    )
}

function update(){
    updateSnake()
    updateFood()
    if(bugExists){
        updateBug()
    }
    checkDeath()
}

function updateSnake() {
    addSegments()
    if(bugExists){
        reduceSegments()
    }
    const inputDirection = getInputDirection()
    for (let i = snakeBody.length - 2; i >= 0; i--){
        snakeBody[i + 1] = { ...snakeBody[i] }
    }
    snakeBody[0].x += inputDirection.x
    if(snakeBody[0].x <1) {snakeBody[0].x = 21}
    if(snakeBody[0].x >21) {snakeBody[0].x = 1}
    snakeBody[0].y += inputDirection.y
    if(snakeBody[0].y <1) {snakeBody[0].y = 21}
    if(snakeBody[0].y >21) {snakeBody[0].y = 1}
}

function addSegments() {
    for(let i = 0; i < newSegments; i++){
        snakeBody.push({...snakeBody[snakeBody.length - 1]})
    }
    newSegments = 0
}

function reduceSegments(){
    if (onSnake(bugBody)){
        snakeBody.pop()
        snakeBody.pop()
    }
}

function updateFood(){

    for (let i=0; i< food.length; i++){
        if (onSnake(food[i])){
            food.splice(i,1);
            expandSnake(EXPANSION_RATE)
            food.push(getRandomFoodPosition())
        }
        if (equalPositions(bugBody, food[i])){
            food.splice(i,1)
            food.push(getRandomFoodPosition())

        }
    }
    if(bugExists && food.length < 11){
        food.push(getRandomFoodPosition())
    }
}

function expandSnake(amount) {
    newSegments += amount
}

function onSnake(position, {ignoreHead= false} ={}) {
    return snakeBody.some((segment,index) => {
        if (ignoreHead && index === 0) return false
        return equalPositions(segment, position)
    })
}

function equalPositions(pos1, pos2) {
    return pos1.x === pos2.x && pos1.y === pos2.y
}

function getSnakeHead() {
    return snakeBody[0]
}

function snakeIntersection() {
    return onSnake(snakeBody[0], {ignoreHead: true})
}

function checkDeath(){

    if(outsideGrid(getSnakeHead())){
        gameOverMessage = "YOU LOST SNAKE"
        gameOver = true
    }

    if(!bugExists && snakeIntersection()){
        gameOverMessage = "YOU LOST SNAKE"
        gameOver = true
    }

    if(bugExists){
        if (snakeAteBug()){
            gameOverMessage = "YOU LOST BUG THE SNAKE ATE YOU"
            gameOver = true
        }
        if (snakeBody.length < 4){
            gameOverMessage = "YOU LOST SNAKE THE BUG ATE YOU"
            gameOver = true
        }
    }
}

function snakeAteBug(){
    return equalPositions(getSnakeHead(), bugBody)
}

function draw(){
    gameBoard.innerHTML = ''
    drawSnake(gameBoard)
    if(bugExists){
        drawBug(gameBoard)
    }
    drawFood(gameBoard)
}

function drawFood(gameBoard){
    food.forEach(segment => {
        const foodElement = document.createElement('div')
        foodElement.style.gridRowStart = segment.y
        foodElement.style.gridColumnStart = segment.x
        foodElement.classList.add('food')
        gameBoard.appendChild(foodElement)
    })
}

function getRandomFoodPosition(){
    let newFoodPosition
    while (newFoodPosition == null || onSnake(newFoodPosition)){
        newFoodPosition = randomGridPosition()
    }
    console.log(newFoodPosition)
    return newFoodPosition
}

function updateBug() {
    const bugDirection = getBugDirection()

    bugBody.x += bugDirection.x
    if(bugBody.x <1) {bugBody.x = 21}
    if(bugBody.x >21) {bugBody.x = 1}
    bugBody.y += bugDirection.y
    if(bugBody.y <1) {bugBody.y = 21}
    if(bugBody.y >21) {bugBody.y = 1}
}

function getInputDirection(){
    lastInputDirection = inputDirection
    return inputDirection
}

function getBugDirection(){
    lastBugDirection = bugInputDirection
    return bugInputDirection
}

function drawSnake(gameBoard) {
    snakeBody.forEach(segment => {
        const snakeElement = document.createElement('div')
        snakeElement.style.gridRowStart = segment.y
        snakeElement.style.gridColumnStart = segment.x
        snakeElement.classList.add('snake')
        gameBoard.appendChild(snakeElement)
    })
}

// Hooks
onMounted(() => {
    window.addEventListener('keydown', onKeyDown)
    window.requestAnimationFrame(main)
})

// Event handlers
function onKeyDown(e) {
    switch (e.key){
        case "w":
            if (lastInputDirection.y !==0) break
            inputDirection = {x:0, y:-1}
            break
        case 's':
            if (lastInputDirection.y !==0) break
            inputDirection = {x:0, y:+1}
            break
        case 'a':
            if (lastInputDirection.x !==0) break
            inputDirection = {x:-1, y:0}
            break
        case 'd':
            if (lastInputDirection.x !==0) break
            inputDirection = {x:1, y:0}
            break
        case "ArrowUp":
            bugExists = true
            if (lastBugDirection.y  < 0)
            {
                bugInputDirection = {x:0, y:-2}
                break
            }
            bugInputDirection = {x: 0, y: -1}
            break

        case 'ArrowDown':
            if (lastBugDirection.y > 0)
            {
                bugInputDirection = {x:0, y:+2}
                break
            }
            bugInputDirection = {x:0, y:+1}
            break
        case 'ArrowLeft':
            if (lastBugDirection.x < 0)
            {
                bugInputDirection = {x:-2, y:0}
                break
            }
            bugInputDirection = {x:-1, y:0}
            break
        case 'ArrowRight':
            if (lastBugDirection.x > 0)
            {
                bugInputDirection = {x:2, y:0}
                break
            }
            bugInputDirection = {x:1, y:0}
            break
    }
}

// Main game loop
function main(currentTime) {
    if (gameOver){
        if (confirm(gameOverMessage)){
            window.location = ""
        }
        return
    }

    window.requestAnimationFrame(main)
    const secondsSinceLastRender = (currentTime - lastRenderTime) / 1000
    if (secondsSinceLastRender < 1 / SNAKE_SPEED) return

    lastRenderTime = currentTime

    update()
    draw()
}

</script>

<template>
    <div id="game-board" ref="gameBoard"></div>
</template>

<style scoped>
/*body {*/
/*    height: 100vh;*/
/*    width: 100vw;*/
/*    display: flex;*/
/*    justify-content: center;*/
/*    align-items: center;*/
/*    margin: 0;*/
/*    background-color: black;*/
/*}*/

#game-board {
    background-color: #fecd1a;
    width: 500px;
    height: 500px;
    display: grid;
    grid-template-rows: repeat(21, 1fr);
    grid-template-columns: repeat(21, 1fr);
}

.snake {
    background-color: #D9261C;
    /*background-image: url("images/bertschilogo.ico");*/
    background-size: cover;

}

.bug {
    background-color: black;
    /*background-image: url("images/bug.ico");*/
    background-size: cover;

}

.food {
    background-color: green;
    /*background-image: url("images/Container.ico");*/
    background-size: cover;
}
</style>
