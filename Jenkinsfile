pipeline {
    agent any

    environment {
        DOCKER_REGISTRY = 'yourdockerhubusername'
        IMAGE_NAME = 'project-groceri'
    }

    stages {
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    // GANTI 'sh' dengan 'bat' untuk Windows
                    bat 'docker build -t %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID% .'
                }
            }
        }

        stage('Push to Registry') {
            steps {
                script {
                    // Login ke Docker Hub (gunakan bat untuk Windows)
                    withCredentials([string(credentialsId: 'docker-hub-credential', variable: 'DOCKER_PASSWORD')]) {
                        bat 'echo %DOCKER_PASSWORD% | docker login --username %DOCKER_REGISTRY% --password-stdin'
                    }
                    // Push image
                    bat 'docker push %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID%'
                    bat 'docker push %DOCKER_REGISTRY%/%IMAGE_NAME%:latest'
                }
            }
        }
        
        // Untuk saat ini, COMMENT dulu stage Deploy dan Health Check
        stage('Deploy to Azure') {
            steps {
                echo 'Deploy stage disabled for Windows testing'
            }
        }
        
        stage('Health Check') {
            steps {
                echo 'Health Check stage disabled'
            }
        }
    }
}
