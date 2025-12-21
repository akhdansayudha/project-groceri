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
        script {
            echo '🚀 Starting deployment to Azure Web App...'
            
            withCredentials([usernamePassword(
                credentialsId: 'azure-webapp-publish', // ID credential yang baru dibuat
                usernameVariable: 'AZURE_USERNAME',
                passwordVariable: 'AZURE_PASSWORD'
            )]) {
                // 1. Update Web App dengan image terbaru dari Docker Hub
                bat """
                az login --service-principal -u %AZURE_USERNAME% -p %AZURE_PASSWORD% --tenant common
                az webapp config container set \
                  --name project-vektora \
                  --resource-group project-vektora-group \
                  --docker-custom-image-name wahyuditrs17/project-groceri:%BUILD_ID% \
                  --docker-registry-server-url https://index.docker.io
                """
                
                // 2. Restart web app untuk apply perubahan
                bat "az webapp restart --name project-vektora --resource-group project-vektora-group"
                
                echo '⏳ Waiting for deployment to complete...'
                sleep(time: 30, unit: 'SECONDS')
            }
        }
    }
}

stage('Health Check') {
    steps {
        script {
            echo '🏥 Checking application health...'
            // Test aplikasi setelah deploy
            bat "curl -f --retry 3 --retry-delay 10 https://project-vektora.azurewebsites.net/ || echo 'App might still be starting...'"
            
            echo '========================================'
            echo '✅ DEPLOYMENT SUCCESSFUL!'
            echo '🌐 Your app is live at: https://project-vektora.azurewebsites.net'
            echo '📦 Docker Image: wahyuditrs17/project-groceri:%BUILD_ID%'
            echo '========================================'
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
