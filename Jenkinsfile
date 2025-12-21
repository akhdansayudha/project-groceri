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
            bat "docker build -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID} -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest ."
        }
    }
}

        stage('Push to Registry') {
    steps {
        script {
            withCredentials([usernamePassword(
                credentialsId: 'docker-hub-credential',
                usernameVariable: 'DOCKER_USERNAME',
                passwordVariable: 'DOCKER_PASSWORD'
            )]) {
                // Login ke Docker Hub
                bat "echo ${DOCKER_PASSWORD} | docker login --username ${DOCKER_USERNAME} --password-stdin"
                
                // **TAMBAHKAN: Tag image sebagai 'latest'**
                bat "docker tag ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID} ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest"
                
                // Push image dengan tag BUILD_ID
                bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID}"
                
                // Push image dengan tag 'latest'
                bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest"
            }
        }
    }
}
        
        stage('Deploy to Azure') {
            steps {
                echo 'Deploy stage disabled for now'
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
            echo 'Pipeline selesai'
            cleanWs()
        }
    }
}
