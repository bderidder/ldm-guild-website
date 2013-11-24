package ladanse.website.bean;

import org.hibernate.Session;

import javax.enterprise.context.ApplicationScoped;
import javax.inject.Named;
import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;
import javax.persistence.Persistence;

@Named
@ApplicationScoped
public class JpaSessionBean {
    private EntityManagerFactory emf;

    public JpaSessionBean() {
        emf = Persistence.createEntityManagerFactory("org.ladanse.persistence");

        if (emf == null) {
            System.err.println("EntityManagerFactory was null");
        }
        else {
            System.err.println("EntityManagerFactory created");
        }

    }

    public EntityManager getEntityManager() {
        return emf.createEntityManager();
    }
}
