pipeline {
    agent any

    environment {
        DOCKER_REGISTRY = 'wahyuditrs17'
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
                    bat "docker build -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID} ."
                }
            }
        }

        stage('Push to Registry') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'docker-hub-credential', variable: 'DOCKER_PASSWORD')]) {
                        bat "echo ${DOCKER_PASSWORD} | docker login --username ${env.DOCKER_REGISTRY} --password-stdin"
                    }
                    bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID}"
                    bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest"
                }
            }
        }
        
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
    
    post {
        always {
            echo 'Pipeline selesai - lihat logs untuk detail'
            cleanWs()
        }
    }
}
